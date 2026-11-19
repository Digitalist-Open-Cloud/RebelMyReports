<?php

namespace Piwik\Plugins\RebelMyReports;

use Exception;
use Piwik\API\Request;
use Piwik\Archive\ArchiveInvalidator;
use Piwik\ArchiveProcessor\Parameters;
use Piwik\ArchiveProcessor\Rules;
use Piwik\Category\CategoryList;
use Piwik\Common;
use Piwik\DataAccess\LogAggregator;
use Piwik\Date;
use Piwik\Period\Factory as PeriodFactory;
use Piwik\Piwik;
use Piwik\Plugins\RebelMyReports\Archiving\ArchiveProblems;
use Piwik\Plugins\RebelMyReports\Archiving\ObsoleteRecords;
use Piwik\Plugins\RebelMyReports\Archiving\QueryBuilder;
use Piwik\Plugins\RebelMyReports\Archiving\ValueGroups;
use Piwik\Plugins\RebelMyReports\Categories\ManageReportsSubcategory;
use Piwik\Plugins\SitesManager\API as SitesManagerApi;
use Piwik\Segment;
use Piwik\Site;

/**
 * Validates, stores and removes report definitions.
 */
class ReportManager
{
    private const MAX_NAME_LENGTH = 100;
    private const MAX_DESCRIPTION_LENGTH = 1000;
    private const MAX_VALUE_GROUPS = 50;
    private const MAX_VALUES_PER_GROUP = 200;
    private const MAX_GROUP_VALUE_LENGTH = 255;

    private Model $model;
    private Catalogue $catalogue;
    private QueryBuilder $queryBuilder;
    private ArchiveInvalidator $archiveInvalidator;
    private ObsoleteRecords $obsoleteRecords;

    public function __construct(
        Model $model,
        Catalogue $catalogue,
        QueryBuilder $queryBuilder,
        ArchiveInvalidator $archiveInvalidator,
        ObsoleteRecords $obsoleteRecords
    ) {
        $this->model = $model;
        $this->catalogue = $catalogue;
        $this->queryBuilder = $queryBuilder;
        $this->archiveInvalidator = $archiveInvalidator;
        $this->obsoleteRecords = $obsoleteRecords;
    }

    /**
     * Returns the report if it is available for the site.
     *
     * @throws Exception if there is no such report
     */
    public function getReport(int $idSite, int $idMyReport): array
    {
        $report = $this->model->getReport($idMyReport);

        if (!$report || ($report['idsite'] !== 0 && $report['idsite'] !== $idSite)) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorReportNotFound', $idMyReport));
        }

        return $report;
    }

    /**
     * @param int $idSite the site whose dimensions and metrics the report is built from
     * @param array $definition name, description, report_type, dimensions, metrics, segment_filter, category,
     *                          subcategory (the ID of an existing page, or empty for a page of its own),
     *                          sort_metric, sort_order, row_limit, metric_filters, value_groups,
     *                          visualization
     * @throws Exception if the definition is not valid
     */
    public function addReport(int $idSite, array $definition, bool $allWebsites): int
    {
        $definition = $this->validate($idSite, $definition, $allWebsites);
        $definition['idsite'] = $allWebsites ? 0 : $idSite;

        $idMyReport = $this->model->addReport($definition, Piwik::getCurrentUserLogin());
        $this->scheduleArchiving($this->model->getReport($idMyReport));

        return $idMyReport;
    }

    /**
     * @throws Exception if the definition is not valid
     */
    public function updateReport(int $idSite, array $report, array $definition): void
    {
        $definition = $this->validate($idSite, $definition, $report['idsite'] === 0);
        $definition['idsite'] = $report['idsite'];

        // Name, description, type and page only change how the archived data is shown.
        $changesData = $definition['dimensions'] !== $report['dimensions']
            || $definition['metrics'] !== $report['metrics']
            || $definition['value_groups'] !== $report['value_groups']
            || $definition['segment_filter'] !== $report['segment_filter'];

        $this->model->updateReport($report['idreport'], $definition, $changesData, Piwik::getCurrentUserLogin());

        if ($changesData) {
            $this->discardArchivedData($report);
            $this->scheduleArchiving($this->model->getReport($report['idreport']));
        }
    }

    /**
     * A paused report keeps its archived data, but gets no new data until it is resumed.
     */
    public function pauseReport(array $report): void
    {
        $this->model->setPaused($report['idreport'], true, Piwik::getCurrentUserLogin());
        $this->archiveInvalidator->removeInvalidationsSafely(
            $report['idsite'] === 0 ? 'all' : [$report['idsite']],
            'RebelMyReports',
            Archiver::getRecordName($report)
        );
    }

    /**
     * Has the report archived again, including the time it was paused.
     */
    public function resumeReport(array $report): void
    {
        $this->model->setPaused($report['idreport'], false, Piwik::getCurrentUserLogin());
        $this->scheduleArchiving($this->model->getReport($report['idreport']));
    }

    /**
     * Sets the order of the reports of the site, which is also the order of their pages in the
     * menu and of the reports that share a page.
     *
     * @param int[] $idMyReports the IDs of all reports of the site in their new order
     * @param bool $mayMoveReportsOfAllWebsites whether a report defined for all websites may
     *                                          get another place, which changes it for every site
     * @throws Exception if the IDs are not the reports of the site
     */
    public function reorderReports(int $idSite, array $idMyReports, bool $mayMoveReportsOfAllWebsites): void
    {
        $reports = array_column($this->model->getReports($idSite), null, 'idreport');

        if (count($idMyReports) !== count($reports) || array_diff(array_keys($reports), $idMyReports)) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorReportOrder'));
        }

        // The reports hand their positions around, so reports of other sites keep their place.
        $positions = array_combine($idMyReports, array_column($reports, 'position'));

        foreach ($positions as $idMyReport => $position) {
            $report = $reports[$idMyReport];
            if ($report['idsite'] === 0 && $report['position'] !== $position && !$mayMoveReportsOfAllWebsites) {
                throw new Exception(Piwik::translate('RebelMyReports_ErrorOrderAllWebsites'));
            }
        }

        $this->model->setPositions($positions);
    }

    /**
     * Has the data of the report built again, for example after a metric it uses changed.
     */
    public function rebuildReport(array $report): void
    {
        $this->discardArchivedData($report);
        $this->model->startNewRevision($report['idreport']);
        $this->scheduleArchiving($this->model->getReport($report['idreport']));
    }

    /**
     * @return array[] the reports that show the metric
     */
    public function getReportsUsingMetric(int $idSite, string $metricName): array
    {
        return array_values(array_filter(
            $this->model->getReports($idSite),
            fn (array $report) => in_array($metricName, $report['metrics'], true)
        ));
    }

    public function deleteReport(array $report): void
    {
        $this->model->deleteReport($report['idreport']);
        $this->discardArchivedData($report);
    }

    /**
     * Deletes the reports of a website that no longer exists.
     */
    public function deleteReportsOfSite(int $idSite): void
    {
        foreach ($this->model->deleteReportsOfSite($idSite) as $report) {
            $this->discardArchivedData($report);
        }
    }

    /**
     * @return array the normalized definition
     * @throws Exception if the definition is not valid
     */
    public function validate(int $idSite, array $definition, bool $allWebsites): array
    {
        $name = trim($definition['name']);
        if ($name === '' || mb_strlen($name) > self::MAX_NAME_LENGTH) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorName', self::MAX_NAME_LENGTH));
        }

        $description = trim($definition['description']);
        if (mb_strlen($description) > self::MAX_DESCRIPTION_LENGTH) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorDescription', self::MAX_DESCRIPTION_LENGTH));
        }

        $dimensions = array_values(array_unique($definition['dimensions'], SORT_REGULAR));
        $metrics = array_values(array_unique($definition['metrics'], SORT_REGULAR));
        $this->catalogue->assertCombinable($dimensions, $metrics);

        $type = $definition['report_type'];
        if (!in_array($type, [Model::TYPE_TABLE, Model::TYPE_EVOLUTION, Model::TYPE_KEY_FIGURE], true)) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorReportType'));
        }
        if ($type === Model::TYPE_TABLE && empty($dimensions)) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorTableNeedsDimension'));
        }
        if ($type !== Model::TYPE_TABLE && !empty($dimensions)) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorNoDimensionsAllowed'));
        }
        if ($type === Model::TYPE_KEY_FIGURE && count($metrics) !== 1) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorKeyFigureNeedsOneMetric'));
        }

        if ($allWebsites) {
            $this->assertNotSiteSpecific($dimensions, $metrics);
        }

        $segmentFilter = trim($definition['segment_filter']);
        // Throws if the segment is not valid for the site.
        $segment = new Segment($segmentFilter, [$idSite]);

        // Building the queries finds selections that pass the catalogue but cannot be queried.
        $period = PeriodFactory::build('day', Date::yesterday());
        $logAggregator = new LogAggregator(new Parameters(new Site($idSite), $period, new Segment('', [$idSite])));
        $valueGroups = $this->validateValueGroups($definition['value_groups'] ?? [], $dimensions);

        // The other types have a visualization of their own.
        $visualization = $type === Model::TYPE_TABLE ? ($definition['visualization'] ?? '') : '';
        $visualization = $visualization !== '' ? $visualization : Model::VISUALIZATIONS[0];
        if (!in_array($visualization, Model::VISUALIZATIONS, true)) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorVisualization', implode(', ', Model::VISUALIZATIONS)));
        }
        $this->queryBuilder->buildQueries($logAggregator, $dimensions, $this->queryBuilder->getArchivedMetrics($metrics), $valueGroups);

        $category = trim($definition['category']);
        $subcategory = trim($definition['subcategory']);
        $pages = $this->getPages($idSite);
        $categoryId = $category !== '' ? $category : GetReportData::DEFAULT_CATEGORY;

        if (!isset($pages[$categoryId])) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorCategory', $category));
        }
        if ($subcategory !== '' && !isset($pages[$categoryId]['pages'][$subcategory])) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorPage', $subcategory));
        }

        return [
            'name' => $name,
            'description' => $description,
            'report_type' => $type,
            'dimensions' => $dimensions,
            'metrics' => $metrics,
            'segment_filter' => $segment->getString(),
            'value_groups' => $valueGroups,
            'visualization' => $visualization,
            'category' => $category,
            'subcategory' => $subcategory,
        ] + $this->validateRowSettings($definition, $type, $metrics);
    }

    /**
     * @param string[] $dimensions the dimensions of the report
     * @return array<int, array{dimension: string, name: string, operator: string, values: string[]}>
     * @throws Exception if a group is not valid
     */
    public function validateValueGroups(array $groups, array $dimensions): array
    {
        if (count($groups) > self::MAX_VALUE_GROUPS) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorTooManyValueGroups', self::MAX_VALUE_GROUPS));
        }

        // Matomo stores what it tracks HTML-encoded, and a group name becomes a row label.
        $encode = fn ($text) => Common::sanitizeInputValue(trim(Common::unsanitizeInputValue((string) $text)));

        $valid = [];
        foreach ($groups as $group) {
            $group = is_array($group) ? $group : [];
            $name = $encode($group['name'] ?? '');
            $operator = $group['operator'] ?? '';
            $values = array_values(array_unique(array_filter(
                array_map($encode, (array) ($group['values'] ?? [])),
                fn (string $value) => $value !== '' && mb_strlen($value) <= self::MAX_GROUP_VALUE_LENGTH
            )));

            if (
                !in_array($group['dimension'] ?? '', $dimensions, true)
                || !is_string($operator)
                || !isset(ValueGroups::OPERATORS[$operator])
                || $name === ''
                || mb_strlen($name) > self::MAX_NAME_LENGTH
                || !$values
                || count($values) > self::MAX_VALUES_PER_GROUP
            ) {
                throw new Exception(Piwik::translate('RebelMyReports_ErrorValueGroup', implode(', ', array_keys(ValueGroups::OPERATORS))));
            }

            $valid[] = ['dimension' => $group['dimension'], 'name' => $name, 'operator' => $operator, 'values' => $values];
        }

        return $valid;
    }

    /**
     * @param string[] $metrics
     * @return array the normalized sorting, row limit and metric filters
     * @throws Exception if a setting is not valid
     */
    public function validateRowSettings(array $definition, string $type, array $metrics): array
    {
        $settings = [
            'sort_metric' => '',
            'sort_order' => Model::SORT_DESC,
            'row_limit' => 0,
            'metric_filters' => [],
        ];

        if ($type !== Model::TYPE_TABLE) {
            // The other types have a single row.
            return $settings;
        }

        if ($definition['sort_metric'] !== '' && !in_array($definition['sort_metric'], $metrics, true)) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorRowMetric', $definition['sort_metric']));
        }
        if (!in_array($definition['sort_order'], [Model::SORT_ASC, Model::SORT_DESC], true)) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorSortOrder'));
        }
        if ($definition['row_limit'] < 0) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorRowLimit'));
        }

        $settings['sort_metric'] = $definition['sort_metric'];
        $settings['sort_order'] = $definition['sort_order'];
        $settings['row_limit'] = $definition['row_limit'];

        foreach ($definition['metric_filters'] as $filter) {
            $metric = is_array($filter) ? (string) ($filter['metric'] ?? '') : '';
            if (!in_array($metric, $metrics, true)) {
                throw new Exception(Piwik::translate('RebelMyReports_ErrorRowMetric', $metric));
            }
            if (!in_array($filter['operator'] ?? '', Model::FILTER_OPERATORS, true)) {
                throw new Exception(Piwik::translate('RebelMyReports_ErrorFilterOperator', implode(', ', Model::FILTER_OPERATORS)));
            }
            if (!is_numeric($filter['value'] ?? '')) {
                throw new Exception(Piwik::translate('RebelMyReports_ErrorFilterValue'));
            }

            $settings['metric_filters'][] = ['metric' => $metric, 'operator' => $filter['operator'], 'value' => (float) $filter['value']];
        }

        return $settings;
    }

    /**
     * Returns the reporting categories with the pages a report can be shown on.
     *
     * @return array<string, array{id: string, name: string, pages: array<string, string>}> indexed by category ID,
     *                                                                                      pages as ID => name
     */
    public function getPages(int $idSite): array
    {
        $categories = [];
        foreach (CategoryList::get()->getCategories() as $category) {
            $categories[$category->getId()] = ['id' => $category->getId(), 'name' => $category->getDisplayName(), 'pages' => []];
        }

        $pages = Request::processRequest('API.getReportPagesMetadata', ['idSite' => $idSite, 'format' => 'original'], []);
        foreach ($pages as $page) {
            $categoryId = $page['category']['id'];
            $pageId = $page['subcategory']['id'];

            if (isset($categories[$categoryId]) && $pageId !== ManageReportsSubcategory::ID) {
                $categories[$categoryId]['pages'][$pageId] = $page['subcategory']['name'];
            }
        }

        return $categories;
    }

    /**
     * @param string[] $dimensionIds
     * @param string[] $metricNames
     */
    private function assertNotSiteSpecific(array $dimensionIds, array $metricNames): void
    {
        $dimensions = $this->catalogue->getDimensions();
        foreach ($dimensionIds as $id) {
            if ($this->catalogue->isSiteSpecificDimension($dimensions[$id])) {
                throw new Exception(Piwik::translate('RebelMyReports_ErrorSiteSpecific', $dimensions[$id]->getName()));
            }
        }

        $metrics = $this->catalogue->getMetrics();
        foreach ($metricNames as $name) {
            if ($this->catalogue->isSiteSpecificMetric($metrics[$name])) {
                throw new Exception(Piwik::translate('RebelMyReports_ErrorSiteSpecific', $this->catalogue->getMetricLabel($metrics[$name])));
            }
        }
    }

    /**
     * Marks the data archived for the days of a period as outdated, so that it is built again
     * the next time archiving runs.
     *
     * @param string $period "day", "week", "month", "year" or "range"
     * @param string $date a date in the period, or "start,end" for a range
     * @param int[] $idMyReports empty for all reports
     * @param string $segment a segment that is archived for the website, or empty for all visits
     *                        and every such segment
     * @return int how many reports were invalidated. Paused reports are left alone.
     * @throws Exception if a report does not exist
     */
    public function invalidateReports(string $period, string $date, array $idMyReports, string $segment = ''): int
    {
        $reports = array_column($this->model->getAllReports(), null, 'idreport');
        if ($idMyReports) {
            foreach ($idMyReports as $idMyReport) {
                if (!isset($reports[$idMyReport])) {
                    throw new Exception(Piwik::translate('RebelMyReports_ErrorReportNotFound', $idMyReport));
                }
            }
            $reports = array_intersect_key($reports, array_flip($idMyReports));
        }

        $range = PeriodFactory::build($period, $date);
        // Tomorrow in UTC is today for the websites ahead of UTC.
        $end = Date::factory('tomorrow');
        $end = $range->getDateEnd()->isLater($end) ? $end : $range->getDateEnd();
        $days = [];
        for ($day = $range->getDateStart(); !$day->isLater($end); $day = $day->addDay(1)) {
            $days[] = $day;
        }

        $allSites = SitesManagerApi::getInstance()->getAllSitesId();
        $invalidated = 0;
        foreach ($reports as $report) {
            if ($report['paused'] || !$days) {
                continue;
            }

            $name = 'RebelMyReports.' . Archiver::getRecordName($report);
            $isInvalidated = false;
            foreach ($report['idsite'] === 0 ? $allSites : [$report['idsite']] as $idSite) {
                foreach ($this->getSegmentsToInvalidate($idSite, $segment) as $siteSegment) {
                    $this->archiveInvalidator->markArchivesAsInvalidated([$idSite], $days, 'day', $siteSegment, false, false, $name);
                    $isInvalidated = true;
                }
            }
            $invalidated += (int) $isInvalidated;
        }

        return $invalidated;
    }

    /**
     * @return Segment[] all visits and the segments archived for the website, or only the
     *                   given segment if it is one of them
     */
    private function getSegmentsToInvalidate(int $idSite, string $segment): array
    {
        $segments = [new Segment('', [$idSite])];
        foreach (Rules::getSegmentsToProcess([$idSite]) as $definition) {
            $segments[] = new Segment($definition, [$idSite]);
        }

        if ($segment === '') {
            return $segments;
        }

        $hash = (new Segment($segment, [$idSite]))->getHash();

        return array_filter(array_slice($segments, 1), fn (Segment $archived) => $archived->getHash() === $hash);
    }

    /**
     * Has core archive the report for past periods the next time archiving runs.
     */
    private function scheduleArchiving(array $report): void
    {
        if ($report['paused']) {
            // Resuming the report schedules it.
            return;
        }

        $idSites = $report['idsite'] === 0 ? 'all' : [$report['idsite']];

        $this->archiveInvalidator->scheduleReArchiving($idSites, 'RebelMyReports', Archiver::getRecordName($report));
    }

    /**
     * Drops what is waiting to be archived for the report's current record and has the data
     * archived for it removed.
     */
    private function discardArchivedData(array $report): void
    {
        $idSites = $report['idsite'] === 0 ? 'all' : [$report['idsite']];
        $recordName = Archiver::getRecordName($report);

        $this->archiveInvalidator->removeInvalidationsSafely($idSites, 'RebelMyReports', $recordName);
        $this->obsoleteRecords->add($recordName, $report['idsite']);
        (new ArchiveProblems())->forget($recordName);
    }
}
