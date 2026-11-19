<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Archive;
use Piwik\ArchiveProcessor\Parameters;
use Piwik\Common;
use Piwik\DataAccess\LogAggregator;
use Piwik\DataTable;
use Piwik\DataTable\DataTableInterface;
use Piwik\Date;
use Piwik\Period\Factory as PeriodFactory;
use Piwik\Piwik;
use Piwik\Plugin\ArchivedMetric;
use Piwik\Plugin\ProcessedMetric;
use Piwik\Plugins\RebelMyReports\Archiving\ArchiveProblems;
use Piwik\Plugins\RebelMyReports\Archiving\ArchiveStatus;
use Piwik\Plugins\RebelMyReports\Archiving\ReportTableBuilder;
use Piwik\Plugins\RebelMyReports\DataTable\Filter\ApplyRowRules;
use Piwik\Plugins\RebelMyReports\DataTable\Filter\FormatDimensionLabels;
use Piwik\Plugins\RebelMyReports\Metrics\CalculatedMetric;
use Piwik\Plugins\RebelMyReports\Metrics\MetricManager;
use Piwik\Plugins\RebelMyReports\Metrics\MetricModel;
use Piwik\Segment;
use Piwik\Site;

/**
 * Build your own reports from any combination of dimensions and metrics.
 *
 * @method static \Piwik\Plugins\RebelMyReports\API getInstance()
 */
class API extends \Piwik\Plugin\API
{
    public const LEVEL_METADATA = 'dimension_level';

    public const PREVIEW_VALUE_METADATA = 'graph_value';

    private const PREVIEW_ROWS = 10;
    private const DIMENSION_VALUES = 500;

    private Model $model;
    private ReportManager $reportManager;
    private Catalogue $catalogue;
    private ReportTableBuilder $tableBuilder;
    private MetricModel $metricModel;
    private MetricManager $metricManager;
    private Duplicator $duplicator;
    private ArchiveStatus $archiveStatus;

    public function __construct(
        Model $model,
        ReportManager $reportManager,
        Catalogue $catalogue,
        ReportTableBuilder $tableBuilder,
        MetricModel $metricModel,
        MetricManager $metricManager,
        Duplicator $duplicator,
        ArchiveStatus $archiveStatus
    ) {
        $this->model = $model;
        $this->reportManager = $reportManager;
        $this->catalogue = $catalogue;
        $this->tableBuilder = $tableBuilder;
        $this->metricModel = $metricModel;
        $this->metricManager = $metricManager;
        $this->duplicator = $duplicator;
        $this->archiveStatus = $archiveStatus;
    }

    /**
     * Returns the reports of a site, including the reports defined for all websites.
     *
     * @return array[]
     */
    public function getReports(int $idSite): array
    {
        Piwik::checkUserHasViewAccess($idSite);

        return array_map(fn (array $report) => $this->forOutput($report, $idSite), $this->model->getReports($idSite));
    }

    /**
     * Returns the definition of one report.
     *
     * @throws \Exception if the site has no such report
     */
    public function getReport(int $idSite, int $idMyReport): array
    {
        Piwik::checkUserHasViewAccess($idSite);

        return $this->forOutput($this->reportManager->getReport($idSite, $idMyReport), $idSite);
    }

    /**
     * Returns for each report of the site which days it has archived data for.
     *
     * @return array[] one entry per report: idreport, first_date and last_date (the first and
     *                 the last day with archived data, null when there is none), is_queued
     *                 (whether more is waiting to be archived) and problem (null, or type and
     *                 period of the last period that could not be archived: "timeout" when a
     *                 query ran too long, "truncated" when it had more rows than a report
     *                 reads, or "failed")
     */
    public function getArchiveStatus(int $idSite): array
    {
        Piwik::checkUserHasViewAccess($idSite);

        return array_values($this->archiveStatus->get($this->model->getReports($idSite), $idSite));
    }

    /**
     * Creates a report. Its data is archived the next time archiving runs.
     *
     * @param string $reportType "table" (needs at least one dimension), "evolution" (no dimension) or
     *                           "keyfigure" (one metric, no dimension)
     * @param string[] $metrics metric IDs as returned by getAvailableMetrics
     * @param string[] $dimensions dimension IDs as returned by getAvailableDimensions, 3 at most unless
     *                             configured otherwise.
     *                             Dimensions after the first become subtables.
     * @param string $segmentFilter a segment the report is always restricted to
     * @param string $category ID of the reporting category to show the report in
     * @param string $subcategory ID of an existing reporting page in the category to add the report to.
     *                            By default the report gets a page of its own.
     * @param bool $allWebsites create the report for all websites, requires super user access
     * @param string $sortMetric the metric a table report is sorted by. By default the first metric.
     * @param string $sortOrder "desc" or "asc"
     * @param int $rowLimit how many rows of each level a table report keeps after sorting, 0 for all
     * @param array[] $metricFilters conditions a row of a table report has to meet all of to be
     *                               kept, each with "metric", "operator" ("gt", "gte", "lt", "lte" or
     *                               "eq") and "value". A rate of 50% is 0.5.
     * @param array[] $valueGroups groups that count several values of a dimension as one row, each
     *                             with "dimension", "name", "operator" ("equals", "contains",
     *                             "starts" or "ends") and "values" (a list, as Matomo stores them).
     *                             The first group a value matches decides.
     * @param string $visualization what a table report opens as: "table", "graphVerticalBar",
     *                              "graphPie" or "cloud". The viewer can still switch.
     * @return int the ID of the new report
     */
    public function addReport(
        int $idSite,
        string $name,
        string $reportType,
        array $metrics,
        array $dimensions = [],
        string $description = '',
        string $segmentFilter = '',
        string $category = '',
        string $subcategory = '',
        bool $allWebsites = false,
        string $sortMetric = '',
        string $sortOrder = 'desc',
        int $rowLimit = 0,
        array $metricFilters = [],
        array $valueGroups = [],
        string $visualization = 'table'
    ): int {
        Piwik::checkUserHasWriteAccess($idSite);
        if ($allWebsites) {
            Piwik::checkUserHasSuperUserAccess();
        }

        $definition = $this->toDefinition($name, $reportType, $metrics, $dimensions, $description, $segmentFilter, $category, $subcategory) + [
            'value_groups' => $valueGroups,
            'visualization' => $visualization,
            'sort_metric' => $sortMetric,
            'sort_order' => $sortOrder,
            'row_limit' => $rowLimit,
            'metric_filters' => $metricFilters,
        ];

        return $this->reportManager->addReport($idSite, $definition, $allWebsites);
    }

    /**
     * Creates one of the example reports of the documentation page, named in the language of
     * the user. An example with a calculated metric creates that metric too, unless the website
     * has one with the same formula.
     *
     * @param string $example "Countries", "EntryPages", "Regions", "Returning", "Visits" or
     *                        "Channels"
     * @return int the ID of the new report
     */
    public function addExampleReport(int $idSite, string $example): int
    {
        Piwik::checkUserHasWriteAccess($idSite);

        $definition = Examples::DEFINITIONS[$example] ?? null;
        if (!$definition) {
            throw new \Exception(Piwik::translate('RebelMyReports_ErrorExample', implode(', ', array_keys(Examples::DEFINITIONS))));
        }

        if (isset($definition['calculated_metric'])) {
            $formula = $definition['calculated_metric'];
            $existing = array_column((new MetricModel())->getMetrics($idSite), 'idmetric', 'formula');
            $idMetric = $existing[$formula]
                ?? $this->metricManager->addMetric($idSite, Piwik::translate("RebelMyReports_Example{$example}Metric"), $formula, 'number');
            $definition['metrics'][] = CalculatedMetric::NAME_PREFIX . $idMetric;
        }

        $valueGroups = [];
        if (isset($definition['grouped_values'])) {
            $valueGroups[] = [
                'dimension' => $definition['dimensions'][0],
                'name' => Piwik::translate("RebelMyReports_Example{$example}Group"),
                'operator' => 'equals',
                'values' => $definition['grouped_values'],
            ];
        }

        return $this->reportManager->addReport($idSite, $definition + [
            'value_groups' => $valueGroups,
            // Names are stored the way they arrive in a request: HTML-encoded.
            'name' => Common::sanitizeInputValue(Piwik::translate("RebelMyReports_Example{$example}Name")),
            'description' => Common::sanitizeInputValue(Piwik::translate("RebelMyReports_Example{$example}Description")),
            'dimensions' => [],
            'segment_filter' => '',
            'category' => '',
            'subcategory' => '',
            'sort_metric' => '',
            'sort_order' => Model::SORT_DESC,
            'row_limit' => 0,
            'metric_filters' => [],
        ], false);
    }

    /**
     * Changes a report. When the dimensions, metrics or segment filter change, the data archived
     * so far is no longer shown and is built again.
     *
     * @param string[] $metrics
     * @param string[] $dimensions
     * @param string $sortMetric the metric a table report is sorted by. By default the first metric.
     * @param string $sortOrder "desc" or "asc"
     * @param int $rowLimit how many rows of each level a table report keeps after sorting, 0 for all
     * @param array[] $metricFilters conditions a row of a table report has to meet all of to be
     *                               kept, each with "metric", "operator" ("gt", "gte", "lt", "lte" or
     *                               "eq") and "value". A rate of 50% is 0.5.
     * @param array[] $valueGroups groups that count several values of a dimension as one row, each
     *                             with "dimension", "name", "operator" ("equals", "contains",
     *                             "starts" or "ends") and "values" (a list, as Matomo stores them).
     *                             The first group a value matches decides.
     * @param string $visualization what a table report opens as: "table", "graphVerticalBar",
     *                              "graphPie" or "cloud". The viewer can still switch.
     */
    public function updateReport(
        int $idSite,
        int $idMyReport,
        string $name,
        string $reportType,
        array $metrics,
        array $dimensions = [],
        string $description = '',
        string $segmentFilter = '',
        string $category = '',
        string $subcategory = '',
        string $sortMetric = '',
        string $sortOrder = 'desc',
        int $rowLimit = 0,
        array $metricFilters = [],
        array $valueGroups = [],
        string $visualization = 'table'
    ): void {
        $report = $this->getReportToManage($idSite, $idMyReport);
        $definition = $this->toDefinition($name, $reportType, $metrics, $dimensions, $description, $segmentFilter, $category, $subcategory) + [
            'value_groups' => $valueGroups,
            'visualization' => $visualization,
            'sort_metric' => $sortMetric,
            'sort_order' => $sortOrder,
            'row_limit' => $rowLimit,
            'metric_filters' => $metricFilters,
        ];

        $this->reportManager->updateReport($idSite, $report, $definition);
    }

    public function deleteReport(int $idSite, int $idMyReport): void
    {
        $this->reportManager->deleteReport($this->getReportToManage($idSite, $idMyReport));
    }

    /**
     * Marks the archived data of reports as outdated for a period. It is built again the next
     * time archiving runs. Paused reports are left alone. Requires super user access.
     *
     * @param string $period "day", "week", "month", "year" or "range"
     * @param string $date a date in the period, or "start,end" for a range
     * @param int[] $idMyReports defaults to all reports of all websites
     * @param string $segmentFilter a stored segment that is archived, to invalidate only its data.
     *                              By default the data of all visits and of every such segment.
     * @return int how many reports were invalidated
     */
    public function invalidateReports(string $period, string $date, array $idMyReports = [], string $segmentFilter = ''): int
    {
        Piwik::checkUserHasSuperUserAccess();

        return $this->reportManager->invalidateReports(
            $period,
            $date,
            array_map('intval', $idMyReports),
            Common::unsanitizeInputValue($segmentFilter)
        );
    }

    /**
     * Pauses a report: it keeps its archived data, but gets no new data until it is resumed.
     */
    public function pauseReport(int $idSite, int $idMyReport): void
    {
        $this->reportManager->pauseReport($this->getReportToManage($idSite, $idMyReport));
    }

    /**
     * Resumes a paused report. The data of the time it was paused is archived the next time
     * archiving runs.
     */
    public function resumeReport(int $idSite, int $idMyReport): void
    {
        $this->reportManager->resumeReport($this->getReportToManage($idSite, $idMyReport));
    }

    /**
     * Sets the order of the reports in the list of reports. It is also the order of their pages
     * in the menu and of the reports that share a page.
     *
     * @param int[] $idMyReports the IDs of all reports of the site in their new order. Only a
     *                           super user can give a report for all websites another place.
     */
    public function setReportOrder(int $idSite, array $idMyReports): void
    {
        Piwik::checkUserHasWriteAccess($idSite);

        $this->reportManager->reorderReports($idSite, array_map('intval', $idMyReports), Piwik::hasUserSuperUserAccess());
    }

    /**
     * Copies a report. Calculated metrics it shows are copied to the other website with it,
     * unless that website already has the same metric.
     *
     * @param int $idDestinationSite the website to copy the report to. By default the report
     *                               is copied within its website, with "(copy)" in its name.
     * @return int the ID of the new report
     */
    public function duplicateReport(int $idSite, int $idMyReport, int $idDestinationSite = 0): int
    {
        Piwik::checkUserHasViewAccess($idSite);
        $idDestinationSite = $idDestinationSite ?: $idSite;
        Piwik::checkUserHasWriteAccess($idDestinationSite);

        return $this->duplicator->duplicateReport($this->reportManager->getReport($idSite, $idMyReport), $idDestinationSite);
    }

    /**
     * Returns the dimensions a report of the site can use.
     *
     * @return array[] each with id, name, category, type and siteSpecific
     */
    public function getAvailableDimensions(int $idSite): array
    {
        Piwik::checkUserHasViewAccess($idSite);

        $dimensions = [];
        foreach ($this->catalogue->getDimensions() as $id => $dimension) {
            $dimensions[] = [
                'id' => $id,
                'name' => $dimension->getName(),
                'category' => Piwik::translate((string) $dimension->getCategoryId()),
                'type' => $dimension->getType(),
                'siteSpecific' => $this->catalogue->isSiteSpecificDimension($dimension),
            ];
        }

        return $dimensions;
    }

    /**
     * Returns the metrics a report of the site can use.
     *
     * @return array[] each with id, name, category, siteSpecific and usableInFormula
     */
    public function getAvailableMetrics(int $idSite): array
    {
        Piwik::checkUserHasViewAccess($idSite);

        $metrics = [];
        foreach ($this->catalogue->getMetrics() as $name => $metric) {
            $metrics[] = [
                'id' => $name,
                'name' => $this->catalogue->getMetricLabel($metric),
                'category' => Piwik::translate((string) $metric->getCategoryId()),
                'siteSpecific' => $this->catalogue->isSiteSpecificMetric($metric),
                'usableInFormula' => $metric instanceof ArchivedMetric,
            ];
        }

        // The website's own metrics come first, so their group is at the top of a picker.
        usort($metrics, fn (array $a, array $b) => $this->isCalculated($b) <=> $this->isCalculated($a));

        return $metrics;
    }

    /**
     * Returns the calculated metrics of a site.
     *
     * @return array[] each with idmetric, id (the metric ID to use in reports), name, formula and format
     */
    public function getCalculatedMetrics(int $idSite): array
    {
        Piwik::checkUserHasViewAccess($idSite);

        return array_map([$this, 'addMetricId'], $this->metricModel->getMetrics($idSite));
    }

    /**
     * Creates a metric that is calculated from other metrics, to use in the reports of the site.
     *
     * @param string $formula arithmetic over metric IDs as returned by getAvailableMetrics, with
     *                        numbers, + - * / and parentheses, eg, "pageviews / nb_visits".
     *                        Only metrics that are archived can be used, not rates and averages.
     * @param string $metricFormat how values are shown: "number", "percent" (0.25 is shown as
     *                             25%), "duration" (seconds) or "money"
     * @return int the ID of the new metric
     */
    public function addCalculatedMetric(int $idSite, string $name, string $formula, string $metricFormat = 'number'): int
    {
        Piwik::checkUserHasWriteAccess($idSite);

        return $this->metricManager->addMetric($idSite, $name, Common::unsanitizeInputValue($formula), $metricFormat);
    }

    /**
     * Changes a calculated metric. When the formula changes, the reports that show the metric
     * are built again.
     */
    public function updateCalculatedMetric(int $idSite, int $idMetric, string $name, string $formula, string $metricFormat = 'number'): void
    {
        Piwik::checkUserHasWriteAccess($idSite);

        $metric = $this->metricManager->getMetric($idSite, $idMetric);
        $this->metricManager->updateMetric($metric, $name, Common::unsanitizeInputValue($formula), $metricFormat);
    }

    /**
     * Deletes a calculated metric. A metric that a report still shows cannot be deleted.
     */
    public function deleteCalculatedMetric(int $idSite, int $idMetric): void
    {
        Piwik::checkUserHasWriteAccess($idSite);

        $this->metricManager->deleteMetric($this->metricManager->getMetric($idSite, $idMetric));
    }

    /**
     * Copies a calculated metric.
     *
     * @param int $idDestinationSite the website to copy the metric to. By default the metric
     *                               is copied within its website, with "(copy)" in its name.
     * @return int the ID of the new metric
     */
    public function duplicateCalculatedMetric(int $idSite, int $idMetric, int $idDestinationSite = 0): int
    {
        Piwik::checkUserHasViewAccess($idSite);
        $idDestinationSite = $idDestinationSite ?: $idSite;
        Piwik::checkUserHasWriteAccess($idDestinationSite);

        return $this->duplicator->duplicateMetric($this->metricManager->getMetric($idSite, $idMetric), $idDestinationSite);
    }

    /**
     * Returns the reporting categories a report can be shown in, with their pages.
     *
     * @return array[] each with id, name and pages (each with id and name)
     */
    public function getAvailableCategories(int $idSite): array
    {
        Piwik::checkUserHasViewAccess($idSite);

        $categories = [];
        foreach ($this->reportManager->getPages($idSite) as $category) {
            $pages = [];
            foreach ($category['pages'] as $id => $name) {
                $pages[] = ['id' => $id, 'name' => $name];
            }
            $categories[] = ['id' => $category['id'], 'name' => $category['name'], 'pages' => $pages];
        }

        return $categories;
    }

    /**
     * Returns the archived data of a report.
     *
     * @param string|false $segment
     * @param int|string|false $idSubtable
     * @return DataTable|DataTable\Map
     */
    public function getReportData(
        int $idSite,
        string $period,
        string $date,
        int $idMyReport,
        $segment = false,
        bool $expanded = false,
        bool $flat = false,
        $idSubtable = false
    ): DataTableInterface {
        Piwik::checkUserHasViewAccess($idSite);

        $report = $this->reportManager->getReport($idSite, $idMyReport);
        $recordName = Archiver::getRecordName($report);
        $dimensions = $this->getDimensions($report['dimensions']);

        // Flattening happens after this method so that labels are formatted per level first.
        $table = Archive::createDataTableFromArchive($recordName, $idSite, $period, $date, $segment, $expanded || $flat, false, $idSubtable);

        $level = 0;
        if (!empty($idSubtable)) {
            $level = $this->getLevelOfSubtable($recordName, $idSite, $period, $date, $segment, (int) $idSubtable);
        }

        $table->filter(FormatDimensionLabels::class, [$dimensions, $idSite, $level, $report['value_groups']]);
        if ($dimensions) {
            $table->filter(ApplyRowRules::class, [$report, $this->catalogue]);
        }
        // Tells the report view which dimension the rows of a subtable belong to.
        $table->filter(function (DataTable $dataTable) use ($level) {
            $dataTable->setMetadata(self::LEVEL_METADATA, $level);
        });

        return $dimensions ? $table : $this->toSimple($table);
    }

    /**
     * Core only compares a table without labels with another period or segment if it is a
     * Simple table, like the "get" reports of core return.
     *
     * @param DataTable|DataTable\Map $table
     * @return DataTable|DataTable\Map
     */
    private function toSimple(DataTableInterface $table): DataTableInterface
    {
        if ($table instanceof DataTable\Map) {
            foreach ($table->getDataTables() as $label => $tableOfPeriod) {
                $table->addTable($this->toSimple($tableOfPeriod), $label);
            }

            return $table;
        }

        $simple = new DataTable\Simple();
        $simple->setAllTableMetadata($table->getAllTableMetadata());
        if ($table->getFirstRow()) {
            $simple->addRow($table->getFirstRow());
        }

        return $simple;
    }

    /**
     * Returns the values a dimension had most often in the last 7 days, as Matomo stores them and as
     * they are shown. Meant for choosing the values of a value group.
     *
     * @param string $dimension a dimension ID as returned by getAvailableDimensions
     * @param string $metric the metric that decides which values come first
     * @return array<int, array{value: string, label: string}>
     */
    public function getDimensionValues(int $idSite, string $dimension, string $metric = 'nb_visits'): array
    {
        Piwik::checkUserHasWriteAccess($idSite);

        $this->catalogue->assertCombinable([$dimension], [$metric]);

        $period = PeriodFactory::build('range', 'last7');
        $logAggregator = new LogAggregator(new Parameters(new Site($idSite), $period, new Segment('', [$idSite])));
        $logAggregator->setQueryOriginHint('RebelMyReportsPreview');

        $table = $this->tableBuilder->build($logAggregator, [$dimension], [$metric], (new Configuration())->getPreviewMaxExecutionTime());
        $table->filter('Limit', [0, self::DIMENSION_VALUES]);
        $values = $table->getColumn('label');
        $table->filter(FormatDimensionLabels::class, [$this->getDimensions([$dimension]), $idSite]);

        $result = [];
        foreach ($table->getColumn('label') as $index => $label) {
            if ($values[$index] !== '') {
                $result[] = ['value' => (string) $values[$index], 'label' => (string) $label];
            }
        }

        return $result;
    }

    /**
     * Returns the first rows of a report definition for one day, queried from the logs
     * without archiving. Meant for trying out a definition before saving it.
     *
     * @param string[] $metrics
     * @param string[] $dimensions
     * @param string $sortMetric the sorting, row limit, metric filters and value groups as in
     *                           addReport
     */
    public function previewReport(
        int $idSite,
        array $metrics,
        array $dimensions = [],
        string $segmentFilter = '',
        string $date = 'yesterday',
        string $sortMetric = '',
        string $sortOrder = 'desc',
        int $rowLimit = 0,
        array $metricFilters = [],
        array $valueGroups = []
    ): DataTable {
        Piwik::checkUserHasWriteAccess($idSite);

        $this->catalogue->assertCombinable($dimensions, $metrics);
        $valueGroups = $this->reportManager->validateValueGroups($valueGroups, $dimensions);
        $rowSettings = $this->reportManager->validateRowSettings([
            'sort_metric' => $sortMetric,
            'sort_order' => $sortOrder,
            'row_limit' => $rowLimit,
            'metric_filters' => $metricFilters,
        ], $dimensions ? Model::TYPE_TABLE : Model::TYPE_EVOLUTION, $metrics);

        $period = PeriodFactory::build('day', Date::factory($date));
        $segment = new Segment(Common::unsanitizeInputValue($segmentFilter), [$idSite], $period->getDateTimeStart(), $period->getDateTimeEnd());
        $logAggregator = new LogAggregator(new Parameters(new Site($idSite), $period, $segment));
        $logAggregator->setQueryOriginHint('RebelMyReportsPreview');

        $maxExecutionTime = (new Configuration())->getPreviewMaxExecutionTime();
        try {
            $table = $this->tableBuilder->build($logAggregator, $dimensions, $metrics, $maxExecutionTime, $valueGroups);
        } catch (\Exception $e) {
            if ((new ArchiveProblems())->isTimeout($e)) {
                throw new \Exception(Piwik::translate('RebelMyReports_PreviewTimeout', $maxExecutionTime));
            }
            throw $e;
        }
        if ($dimensions) {
            $table->filter(ApplyRowRules::class, [['metrics' => $metrics] + $rowSettings, $this->catalogue]);
        }
        $table->filter(FormatDimensionLabels::class, [$this->getDimensions($dimensions), $idSite, 0, $valueGroups]);

        if (count($dimensions) > 1) {
            // Core can only flatten the tables of saved reports.
            $nested = $table;
            $table = new DataTable();
            $table->addRowsFromArray($this->getLeafRows($nested));
        }
        $table->filter('Limit', [0, self::PREVIEW_ROWS]);

        // The unformatted value of the first metric, to draw the preview of a graph with.
        $first = $this->catalogue->findMetric($metrics[0]);
        foreach ($table->getRowsWithoutSummaryRow() as $row) {
            $value = $first instanceof ProcessedMetric ? $first->compute($row) : $row->getColumn($metrics[0]);
            $row->setMetadata(self::PREVIEW_VALUE_METADATA, (float) $value);
        }

        $available = $this->catalogue->getMetrics();
        $computed = [];
        foreach ($metrics as $name) {
            if ($available[$name] instanceof ProcessedMetric) {
                $computed[] = $available[$name];
            }
        }
        $table->setMetadata(DataTable::EXTRA_PROCESSED_METRICS_METADATA_NAME, $computed);

        return $table;
    }

    /**
     * Adds the IDs of the reporting page the report is shown on and removes what the user
     * may not see.
     */
    private function forOutput(array $report, int $idSite): array
    {
        $report['category_id'] = GetReportData::getCategoryIdOf($report);
        $report['subcategory_id'] = GetReportData::getSubcategoryIdOf($report);

        // Like core, which only tells users who can change something who else is a user.
        if (!Piwik::isUserHasWriteAccess($idSite)) {
            unset($report['created_by'], $report['updated_by']);
        }

        return $report;
    }

    /**
     * Returns the rows of the last dimension, labelled with the labels of the rows above them.
     *
     * @return DataTable\Row[]
     */
    private function getLeafRows(DataTable $table, string $parentLabel = ''): array
    {
        $rows = [];
        foreach ($table->getRowsWithoutSummaryRow() as $row) {
            $label = ($parentLabel === '' ? '' : $parentLabel . ' - ') . $row->getColumn('label');
            $subtable = $row->getSubtable();

            if ($subtable) {
                $rows = array_merge($rows, $this->getLeafRows($subtable, $label));
            } else {
                $rows[] = new DataTable\Row([DataTable\Row::COLUMNS => ['label' => $label] + $row->getColumns()]);
            }
        }

        return $rows;
    }

    private function isCalculated(array $metric): bool
    {
        return strpos($metric['id'], CalculatedMetric::NAME_PREFIX) === 0;
    }

    private function addMetricId(array $metric): array
    {
        $metric['id'] = CalculatedMetric::getNameOf($metric['idmetric']);

        return $metric;
    }

    private function getReportToManage(int $idSite, int $idMyReport): array
    {
        Piwik::checkUserHasWriteAccess($idSite);

        $report = $this->reportManager->getReport($idSite, $idMyReport);
        if ($report['idsite'] === 0) {
            Piwik::checkUserHasSuperUserAccess();
        }

        return $report;
    }

    private function toDefinition(
        string $name,
        string $reportType,
        array $metrics,
        array $dimensions,
        string $description,
        string $segmentFilter,
        string $category,
        string $subcategory
    ): array {
        return [
            'name' => $name,
            'description' => $description,
            'report_type' => $reportType,
            'dimensions' => $dimensions,
            'metrics' => $metrics,
            // Segment operators like ">" arrive HTML-encoded.
            'segment_filter' => Common::unsanitizeInputValue($segmentFilter),
            'category' => $category,
            'subcategory' => $subcategory,
        ];
    }

    /**
     * @param string[] $dimensionIds
     * @return \Piwik\Columns\Dimension[]
     */
    private function getDimensions(array $dimensionIds): array
    {
        $available = $this->catalogue->getDimensions();
        $dimensions = [];
        foreach ($dimensionIds as $id) {
            if (isset($available[$id])) {
                $dimensions[] = $available[$id];
            }
        }

        return $dimensions;
    }

    /**
     * A subtable ID does not tell which dimension its rows belong to. A subtable of a first
     * level row is the second level, any other the third.
     *
     * @param string|false $segment
     */
    private function getLevelOfSubtable(string $recordName, int $idSite, string $period, string $date, $segment, int $idSubtable): int
    {
        $root = Archive::createDataTableFromArchive($recordName, $idSite, $period, $date, $segment);

        $tables = $root instanceof DataTable\Map ? $root->getDataTables() : [$root];
        foreach ($tables as $table) {
            foreach ($table->getRows() as $row) {
                if ((int) $row->getIdSubDataTable() === $idSubtable) {
                    return 1;
                }
            }
        }

        return 2;
    }
}
