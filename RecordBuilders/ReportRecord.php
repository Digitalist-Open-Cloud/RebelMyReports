<?php

namespace Piwik\Plugins\RebelMyReports\RecordBuilders;

use Piwik\ArchiveProcessor;
use Piwik\ArchiveProcessor\Parameters;
use Piwik\ArchiveProcessor\Record;
use Piwik\ArchiveProcessor\RecordBuilder;
use Piwik\Container\StaticContainer;
use Piwik\DataAccess\LogAggregator;
use Piwik\Plugins\RebelMyReports\Archiver;
use Piwik\Plugins\RebelMyReports\Archiving\ArchiveProblems;
use Piwik\Plugins\RebelMyReports\Archiving\QueryBuilder;
use Piwik\Plugins\RebelMyReports\Configuration;
use Piwik\Plugins\RebelMyReports\Archiving\ReportTableBuilder;
use Piwik\Segment;
use Piwik\Segment\SegmentExpression;
use Piwik\Log\LoggerInterface;

/**
 * Archives one user-defined report as a blob record. Dimensions after the first become
 * subtables.
 */
class ReportRecord extends RecordBuilder
{
    private array $report;
    private QueryBuilder $queryBuilder;
    private ReportTableBuilder $tableBuilder;

    public function __construct(array $report, QueryBuilder $queryBuilder, ReportTableBuilder $tableBuilder)
    {
        $configuration = new Configuration();
        parent::__construct($configuration->getMaxRows(), $configuration->getMaxRowsInSubtable());

        $this->report = $report;
        $this->queryBuilder = $queryBuilder;
        $this->tableBuilder = $tableBuilder;
    }

    public function isEnabled(ArchiveProcessor $archiveProcessor): bool
    {
        $idSite = $this->report['idsite'];
        $isForSite = $idSite === 0 || in_array($idSite, $archiveProcessor->getParams()->getIdSites());

        if (!$isForSite || $this->report['paused']) {
            return false;
        }

        if ($this->getMetrics() === null) {
            $params = $archiveProcessor->getParams();
            (new ArchiveProblems())->add(
                Archiver::getRecordName($this->report),
                (int) $params->getSite()->getId(),
                ArchiveProblems::FAILED,
                $params->getPeriod()->getRangeString()
            );
            return false;
        }

        return true;
    }

    public function getRecordMetadata(ArchiveProcessor $archiveProcessor): array
    {
        $metrics = $this->getMetrics();
        if ($metrics === null) {
            return [];
        }

        return [
            Record::make(Record::TYPE_BLOB, Archiver::getRecordName($this->report))
                ->setBlobColumnAggregationOps($this->tableBuilder->getAggregationOps($metrics))
                ->setColumnToSortByBeforeTruncation(array_key_first($metrics)),
        ];
    }

    public function buildForNonDayPeriod(ArchiveProcessor $archiveProcessor): void
    {
        foreach ($this->getMetrics() ?? [] as $metric) {
            if ($this->queryBuilder->getAggregationOp($metric) === QueryBuilder::OP_UNIQUE) {
                // Unique counts of single days cannot be added up.
                $this->buildFromLogs($archiveProcessor);
                return;
            }
        }

        // Core reads the days of a period through one object, which only looks up the archives
        // of single reports for the first plugin it is asked about. A processor of our own
        // looks up ours, so days that were archived for the report alone are not left out.
        parent::buildForNonDayPeriod(new ArchiveProcessor(
            $archiveProcessor->getParams(),
            $archiveProcessor->getArchiveWriter(),
            $archiveProcessor->getLogAggregator()
        ));
    }

    public function getQueryOriginHint(): string
    {
        return 'RebelMyReports' . $this->report['idreport'];
    }

    protected function aggregate(ArchiveProcessor $archiveProcessor): array
    {
        $recordName = Archiver::getRecordName($this->report);
        $params = $archiveProcessor->getParams();
        $idSite = (int) $params->getSite()->getId();
        $period = $params->getPeriod()->getRangeString();
        $problems = new ArchiveProblems();

        try {
            $table = $this->tableBuilder->build(
                $this->getLogAggregator($archiveProcessor),
                $this->report['dimensions'],
                $this->report['metrics'],
                (new Configuration())->getMaxExecutionTime(),
                $this->report['value_groups']
            );
        } catch (\Exception $e) {
            // One broken report must not stop the archiving of the site.
            $problems->add($recordName, $idSite, $problems->isTimeout($e) ? ArchiveProblems::TIMEOUT : ArchiveProblems::FAILED, $period);
            $this->logFailure($e);
            return [];
        }

        if ($table->getMetadata(ReportTableBuilder::TRUNCATED_METADATA)) {
            $table->deleteMetadata(ReportTableBuilder::TRUNCATED_METADATA);
            $problems->add($recordName, $idSite, ArchiveProblems::TRUNCATED, $period);
        } else {
            $problems->resolve($recordName, $idSite, $period);
        }

        return [$recordName => $table];
    }

    /**
     * Returns the metrics to archive, or null if the report uses a metric that no longer exists.
     *
     * @return \Piwik\Plugin\ArchivedMetric[]|null
     */
    private function getMetrics(): ?array
    {
        try {
            return $this->queryBuilder->getArchivedMetrics($this->report['metrics']);
        } catch (\Exception $e) {
            $this->logFailure($e);
            return null;
        }
    }

    private function logFailure(\Exception $e): void
    {
        StaticContainer::get(LoggerInterface::class)->error(
            'RebelMyReports: report {idreport} could not be archived: {message}',
            ['idreport' => $this->report['idreport'], 'message' => $e->getMessage()]
        );
    }

    private function getLogAggregator(ArchiveProcessor $archiveProcessor): LogAggregator
    {
        $logAggregator = $archiveProcessor->getLogAggregator();
        $filter = $this->report['segment_filter'];

        if ($filter !== '') {
            $params = $archiveProcessor->getParams();
            $segment = new Segment(
                Segment::combine($params->getSegment()->getString(), SegmentExpression::AND_DELIMITER, $filter),
                $params->getIdSites(),
                $params->getDateTimeStart(),
                $params->getDateTimeEnd()
            );

            $logAggregator = new LogAggregator(new Parameters($params->getSite(), $params->getPeriod(), $segment));
        }

        $logAggregator->setQueryOriginHint($this->getQueryOriginHint());

        return $logAggregator;
    }
}
