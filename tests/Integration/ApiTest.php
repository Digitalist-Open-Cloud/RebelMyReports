<?php

namespace Piwik\Plugins\RebelMyReports\tests\Integration;

use Piwik\API\Request;
use Piwik\ArchiveProcessor\Rules;
use Piwik\Cache;
use Piwik\Common;
use Piwik\Config;
use Piwik\Container\StaticContainer;
use Piwik\CronArchive\ReArchiveList;
use Piwik\Db;
use Piwik\NoAccessException;
use Piwik\Plugins\Goals\API as GoalsApi;
use Piwik\Plugins\RebelMyReports\API;
use Piwik\Plugins\RebelMyReports\Archiver;
use Piwik\Plugins\RebelMyReports\Configuration;
use Piwik\Plugins\RebelMyReports\Examples;
use Piwik\Plugins\RebelMyReports\SystemSettings;
use Piwik\Plugins\RebelMyReports\tests\Fixtures\TrackedVisits;
use Piwik\Plugins\SegmentEditor\API as SegmentEditorApi;
use Piwik\Segment;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\Mock\FakeAccess;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group RebelMyReports
 * @group Plugins
 */
class ApiTest extends IntegrationTestCase
{
    private int $idSite;
    private API $api;

    protected static function configureFixture($fixture)
    {
        parent::configureFixture($fixture);

        $fixture->createSuperUser = true;
    }

    public function setUp(): void
    {
        parent::setUp();

        Fixture::loadAllTranslations();
        FakeAccess::clearAccess(true);

        $this->idSite = Fixture::createWebsite('2024-01-01 00:00:00', $ecommerce = 1);
        Fixture::createWebsite('2024-01-01 00:00:00');
        GoalsApi::getInstance()->addGoal($this->idSite, 'Signup', 'url', '/signup', 'contains');
        $_GET['idSite'] = $this->idSite;

        $this->api = API::getInstance();
    }

    public function tearDown(): void
    {
        unset($_GET['idSite'], $_GET['idMyReport']);
        Fixture::resetTranslations();
        parent::tearDown();
    }

    public function provideContainerConfig()
    {
        return ['Piwik\Access' => new FakeAccess()];
    }

    public function testAddReportStoresTheDefinition()
    {
        $idMyReport = $this->api->addReport(
            $this->idSite,
            ' Pages by resolution ',
            'table',
            ['nb_visits', 'bounce_rate', 'nb_visits'],
            ['Resolution.Resolution', 'Actions.PageUrl'],
            'What people look at',
            'visitCount>1'
        );

        $report = $this->api->getReport($this->idSite, $idMyReport);

        $this->assertSame('Pages by resolution', $report['name']);
        $this->assertSame(['nb_visits', 'bounce_rate'], $report['metrics']);
        $this->assertSame(['Resolution.Resolution', 'Actions.PageUrl'], $report['dimensions']);
        $this->assertSame('visitCount>1', $report['segment_filter']);
        $this->assertSame($this->idSite, $report['idsite']);
        $this->assertCount(1, $this->api->getReports($this->idSite));
    }

    public function testAddingAndChangingTheDataScheduleArchivingOfTheCurrentRecordOnly()
    {
        $idMyReport = $this->addTableReport();
        $this->assertSame(["RebelMyReports_{$idMyReport}_1"], $this->getScheduledRecords());

        $this->api->updateReport($this->idSite, $idMyReport, 'Renamed', 'table', ['hits'], ['Resolution.Resolution']);

        $this->assertSame(["RebelMyReports_{$idMyReport}_2"], $this->getScheduledRecords());
        $this->assertSame('Renamed', $this->api->getReport($this->idSite, $idMyReport)['name']);
    }

    public function testRenamingOrMovingAReportKeepsItsArchivedData()
    {
        $idMyReport = $this->addTableReport();
        $before = $this->api->getReport($this->idSite, $idMyReport);

        $this->api->updateReport(
            $this->idSite,
            $idMyReport,
            'Renamed',
            'table',
            ['nb_visits'],
            ['Resolution.Resolution'],
            'New description',
            '',
            'General_Visitors',
            'General_Overview'
        );

        $after = $this->api->getReport($this->idSite, $idMyReport);

        $this->assertSame('Renamed', $after['name']);
        $this->assertSame('General_Overview', $after['subcategory_id']);
        $this->assertSame($before['revision'], $after['revision']);
        $this->assertSame(["RebelMyReports_{$idMyReport}_1"], $this->getScheduledRecords());
    }

    /**
     * @dataProvider getInvalidDefinitions
     */
    public function testAddReportRejectsInvalidDefinitions(array $changes, string $expectedMessage)
    {
        $definition = $changes + [
            'name' => 'Report',
            'reportType' => 'table',
            'metrics' => ['nb_visits'],
            'dimensions' => ['Resolution.Resolution'],
            'segmentFilter' => '',
            'category' => '',
        ];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->api->addReport(
            $this->idSite,
            $definition['name'],
            $definition['reportType'],
            $definition['metrics'],
            $definition['dimensions'],
            '',
            $definition['segmentFilter'],
            $definition['category']
        );
    }

    public function getInvalidDefinitions(): array
    {
        return [
            'empty name' => [['name' => '  '], 'needs a name'],
            'unknown type' => [['reportType' => 'pie'], 'report type must be'],
            'table without dimension' => [['dimensions' => []], 'at least one dimension'],
            'evolution with dimension' => [['reportType' => 'evolution'], 'Only a table report can have dimensions'],
            'key figure with dimension' => [['reportType' => 'keyfigure'], 'Only a table report can have dimensions'],
            'unknown metric' => [['metrics' => ['sum(1)']], 'Unknown metric'],
            'unknown dimension' => [['dimensions' => ['log_visit.idvisit']], 'Unknown dimension'],
            'invalid segment' => [['segmentFilter' => 'noSuchSegment==1'], 'noSuchSegment'],
            'unknown category' => [['category' => 'Nope_Nope'], 'Unknown reporting category'],
        ];
    }

    public function testReportForAllWebsitesCannotUseMetricsOfOneWebsite()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('only exists for this website');

        $this->api->addReport($this->idSite, 'Goals', 'evolution', ['goal_1_conversion'], [], '', '', '', '', true);
    }

    public function testReportForAllWebsitesIsAvailableOnEveryWebsite()
    {
        $idMyReport = $this->api->addReport($this->idSite, 'Visits', 'evolution', ['nb_visits'], [], '', '', '', '', true);

        $this->assertSame(0, $this->api->getReport(2, $idMyReport)['idsite']);
    }

    public function testReportOfAnotherWebsiteIsNotFound()
    {
        $idMyReport = $this->addTableReport();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('no report with ID');

        $this->api->getReport(2, $idMyReport);
    }

    public function testViewUserCanReadButNotChangeReports()
    {
        $idMyReport = $this->addTableReport();
        FakeAccess::clearAccess(false, [], [$this->idSite]);

        $this->assertCount(1, $this->api->getReports($this->idSite));

        $this->expectException(NoAccessException::class);
        $this->api->deleteReport($this->idSite, $idMyReport);
    }

    public function testWriteUserCannotChangeReportsForAllWebsites()
    {
        $idMyReport = $this->api->addReport($this->idSite, 'Visits', 'evolution', ['nb_visits'], [], '', '', '', '', true);
        FakeAccess::clearAccess(false, [], [], 'writer', [$this->idSite]);

        $this->expectException(NoAccessException::class);
        $this->api->deleteReport($this->idSite, $idMyReport);
    }

    public function testDeleteRemovesTheReport()
    {
        $idMyReport = $this->addTableReport();

        $this->api->deleteReport($this->idSite, $idMyReport);

        $this->assertSame([], $this->api->getReports($this->idSite));
    }

    public function testCatalogueListsNamesAndCategories()
    {
        $dimensions = array_column($this->api->getAvailableDimensions($this->idSite), null, 'id');
        $metrics = array_column($this->api->getAvailableMetrics($this->idSite), null, 'id');

        $this->assertSame('Country', $dimensions['UserCountry.Country']['name']);
        $this->assertSame('Visitor location', $dimensions['UserCountry.Country']['category']);
        $this->assertSame('Visits', $metrics['nb_visits']['name']);
        $this->assertFalse($metrics['nb_visits']['siteSpecific']);
        $this->assertTrue($metrics['goal_1_conversion']['siteSpecific']);
    }

    public function testReportGetsAPageOfItsOwnOrJoinsAnExistingPage()
    {
        $first = $this->api->addReport($this->idSite, 'One', 'table', ['nb_visits'], ['Resolution.Resolution']);
        $ownPage = $this->api->getReport($this->idSite, $first)['subcategory_id'];
        Cache::getTransientCache()->flushAll();

        $second = $this->api->addReport($this->idSite, 'Two', 'evolution', ['nb_visits'], [], '', '', '', $ownPage);
        $third = $this->api->addReport($this->idSite, 'Three', 'evolution', ['hits'], [], '', '', 'General_Visitors', 'General_Overview');

        $this->assertSame('RebelMyReports_MyReports', $this->api->getReport($this->idSite, $first)['category_id']);
        $this->assertSame($ownPage, $this->api->getReport($this->idSite, $second)['subcategory_id']);
        $this->assertSame('General_Visitors', $this->api->getReport($this->idSite, $third)['category_id']);
        $this->assertSame('General_Overview', $this->api->getReport($this->idSite, $third)['subcategory_id']);
    }

    public function testReportCannotJoinAPageOfAnotherCategory()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('does not exist in this category');

        $this->api->addReport($this->idSite, 'One', 'evolution', ['nb_visits'], [], '', '', '', 'General_Overview');
    }

    public function testCategoriesListTheirPages()
    {
        $idMyReport = $this->addTableReport();
        Cache::getTransientCache()->flushAll();

        $categories = array_column($this->api->getAvailableCategories($this->idSite), null, 'id');

        $this->assertSame('Visitors', $categories['General_Visitors']['name']);
        $this->assertContains(['id' => 'General_Overview', 'name' => 'Overview'], $categories['General_Visitors']['pages']);
        $this->assertSame(
            [['id' => "RebelMyReports_page_$idMyReport", 'name' => 'Resolutions']],
            $categories['RebelMyReports_MyReports']['pages']
        );
    }

    public function testKeyFigureHasExactlyOneMetricAndNoDimension()
    {
        $idMyReport = $this->api->addReport($this->idSite, 'Visits', 'keyfigure', ['nb_visits']);
        $this->assertSame('keyfigure', $this->api->getReport($this->idSite, $idMyReport)['report_type']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('exactly one metric');

        $this->api->addReport($this->idSite, 'Two', 'keyfigure', ['nb_visits', 'hits']);
    }

    public function testEventReportHasOneLevelPerDimension()
    {
        TrackedVisits::track($this->idSite);
        $idMyReport = $this->api->addReport(
            $this->idSite,
            'Events',
            'table',
            ['nb_visits'],
            ['Events.EventCategory', 'Events.EventName', 'Events.EventAction']
        );

        $category = $this->requestReportData($idMyReport, ['expanded' => 1])->getRowFromLabel('Video');
        $name = $category->getSubtable()->getRowFromLabel('intro');
        $action = $name->getSubtable()->getRowFromLabel('play');

        $this->assertEquals([1, 1, 1], [$category->getColumn('nb_visits'), $name->getColumn('nb_visits'), $action->getColumn('nb_visits')]);

        // As the report view does: each level is requested by the subtable ID of the row above.
        $idNames = $this->requestReportData($idMyReport)->getRowFromLabel('Video')->getIdSubDataTable();
        $names = $this->requestReportData($idMyReport, ['idSubtable' => $idNames]);
        $actions = $this->requestReportData($idMyReport, ['idSubtable' => $names->getRowFromLabel('intro')->getIdSubDataTable()]);

        $this->assertSame(1, $names->getMetadata('dimension_level'));
        $this->assertSame(['intro'], $names->getColumn('label'));
        $this->assertSame(2, $actions->getMetadata('dimension_level'));
        $this->assertSame(['play'], $actions->getColumn('label'));
    }

    public function testReportDataHasReadableLabelsSegmentsAndComputedMetrics()
    {
        TrackedVisits::track($this->idSite);
        $idMyReport = $this->api->addReport(
            $this->idSite,
            'Converted',
            'table',
            ['nb_visits', 'bounce_rate'],
            ['CoreHome.VisitGoalConverted', 'Actions.PageUrl']
        );

        $table = $this->requestReportData($idMyReport, ['expanded' => 1]);

        // Both visitors convert: visitor 2 a goal after a single page, visitor 1 an order.
        $row = $table->getRowFromLabel('Yes');
        $this->assertEquals(2, $row->getColumn('nb_visits'));
        $this->assertSame('visitConverted==1', $row->getMetadata('segment'));
        $this->assertSame('50%', $row->getColumn('bounce_rate'));

        $page = $row->getSubtable()->getRowFromLabel('example.org/b');
        $this->assertEquals(1, $page->getColumn('nb_visits'));
        $this->assertSame('visitConverted==1;pageUrl==' . urlencode('example.org/b'), $page->getMetadata('segment'));
    }

    public function testSubtableRequestFormatsLabelsOfItsOwnDimension()
    {
        TrackedVisits::track($this->idSite);
        $idMyReport = $this->api->addReport($this->idSite, 'Pages', 'table', ['nb_visits'], ['CoreHome.VisitGoalConverted', 'Actions.PageUrl']);

        $idSubtable = $this->requestReportData($idMyReport)->getRowFromLabel('Yes')->getIdSubDataTable();
        $subtable = $this->requestReportData($idMyReport, ['idSubtable' => $idSubtable]);

        $this->assertEqualsCanonicalizing(['example.org/a', 'example.org/b'], $subtable->getColumn('label'));
    }

    public function testRowEvolutionFindsTheReportByItsRequestParameter()
    {
        TrackedVisits::track($this->idSite);
        $idMyReport = $this->addTableReport();
        Cache::getTransientCache()->flushAll();
        $_GET['idMyReport'] = $idMyReport;

        $evolution = Request::processRequest('API.getRowEvolution', [
            'idSite' => $this->idSite,
            'period' => 'day',
            'date' => TrackedVisits::DAY . ',' . TrackedVisits::NEXT_DAY,
            'apiModule' => 'RebelMyReports',
            'apiAction' => 'getReportData',
            'label' => '1920x1080',
            'format' => 'original',
        ]);

        $visits = [];
        foreach ($evolution['reportData']->getDataTables() as $date => $table) {
            $visits[$date] = (int) $table->getFirstRow()->getColumn('nb_visits');
        }
        $this->assertSame([TrackedVisits::DAY => 1, TrackedVisits::NEXT_DAY => 1], $visits);
    }

    public function testEvolutionReportHasOneRowWithoutLabel()
    {
        TrackedVisits::track($this->idSite);
        $idMyReport = $this->api->addReport($this->idSite, 'Visits', 'evolution', ['nb_visits', 'nb_uniq_visitors']);

        $table = $this->requestReportData($idMyReport);

        $this->assertEquals(['nb_visits' => 2, 'nb_uniq_visitors' => 2], $table->getFirstRow()->getColumns());
    }

    public function testPreviewQueriesTheLogsWithoutSavingAReport()
    {
        TrackedVisits::track($this->idSite);

        $table = Request::processRequest('RebelMyReports.previewReport', [
            'idSite' => $this->idSite,
            'metrics' => ['nb_visits', 'bounce_rate'],
            'dimensions' => ['Resolution.Resolution'],
            'date' => TrackedVisits::DAY,
        ]);

        $this->assertEquals(1, $table->getRowFromLabel('800x600')->getColumn('nb_visits'));
        $this->assertSame('100%', $table->getRowFromLabel('800x600')->getColumn('bounce_rate'));
        $this->assertSame([], $this->api->getReports($this->idSite));
    }

    public function testPreviewFollowsTheRowSettings()
    {
        TrackedVisits::track($this->idSite);
        $request = [
            'idSite' => $this->idSite,
            'metrics' => ['nb_visits'],
            'dimensions' => ['Actions.PageUrl'],
            'date' => TrackedVisits::DAY,
        ];

        $ascending = Request::processRequest('RebelMyReports.previewReport', $request + ['sortOrder' => 'asc']);
        $top = Request::processRequest('RebelMyReports.previewReport', $request + ['rowLimit' => 1]);
        $filtered = Request::processRequest('RebelMyReports.previewReport', $request + [
            'metricFilters' => [['metric' => 'nb_visits', 'operator' => 'lt', 'value' => '2']],
        ]);

        // Visits by page: /a has 2 and /b has 1.
        $this->assertSame(['example.org/b', 'example.org/a'], $ascending->getColumn('label'));
        $this->assertSame(['example.org/a'], $top->getColumn('label'));
        $this->assertSame(['example.org/b'], $filtered->getColumn('label'));
    }

    public function testPreviewOfSeveralDimensionsHasOneRowPerCombination()
    {
        TrackedVisits::track($this->idSite);

        $table = Request::processRequest('RebelMyReports.previewReport', [
            'idSite' => $this->idSite,
            'metrics' => ['pageviews'],
            'dimensions' => ['Resolution.Resolution', 'Actions.PageUrl'],
            'date' => TrackedVisits::DAY,
        ]);

        $this->assertSame(
            ['1920x1080 - example.org/a' => 2, '1920x1080 - example.org/b' => 1, '800x600 - example.org/a' => 1],
            array_map('intval', array_combine($table->getColumn('label'), $table->getColumn('pageviews')))
        );
    }

    public function testRowSettingsAreStoredAndValidated()
    {
        $filters = [
            ['metric' => 'nb_visits', 'operator' => 'gte', 'value' => 2.0],
            ['metric' => 'bounce_rate', 'operator' => 'lt', 'value' => 0.5],
        ];
        $idMyReport = $this->addSortedReport('bounce_rate', 'asc', 5, $filters);

        $report = $this->api->getReport($this->idSite, $idMyReport);
        $settings = array_intersect_key($report, array_flip(['sort_metric', 'sort_order', 'row_limit', 'metric_filters']));
        $this->assertEquals(
            ['sort_metric' => 'bounce_rate', 'sort_order' => 'asc', 'row_limit' => 5, 'metric_filters' => $filters],
            $settings
        );

        $this->expectExceptionMessage('is not a metric of the report');
        $this->addSortedReport('nb_actions');
    }

    public function testRowsAreFilteredSortedAndLimited()
    {
        TrackedVisits::track($this->idSite);

        // Created before the first request, which archives the day.
        $ascending = $this->addSortedReport('nb_visits', 'asc');
        $top = $this->addSortedReport('', 'desc', 1);
        $fewVisits = ['metric' => 'nb_visits', 'operator' => 'lt', 'value' => 2];
        $bounces = ['metric' => 'bounce_rate', 'operator' => 'gt', 'value' => 0.25];
        $filtered = $this->addSortedReport('', 'desc', 0, [$fewVisits]);
        $byRate = $this->addSortedReport('', 'desc', 0, [$bounces]);
        $both = $this->addSortedReport('', 'desc', 0, [$fewVisits, $bounces]);

        // Visits by page: /a has 2 and /b has 1.
        $this->assertSame(['example.org/b', 'example.org/a'], $this->requestReportData($ascending)->getColumn('label'));
        $this->assertSame(['example.org/a'], $this->requestReportData($top)->getColumn('label'));
        $this->assertSame(['example.org/b'], $this->requestReportData($filtered)->getColumn('label'));
        // The bounce rate is not archived: /a 50%, /b 0%.
        $this->assertSame(['example.org/a'], $this->requestReportData($byRate)->getColumn('label'));
        $this->assertSame([], $this->requestReportData($both)->getColumn('label'));
    }

    public function testARowWithoutLogsForAMetricSortsAsZero()
    {
        TrackedVisits::track($this->idSite);
        $idMyReport = $this->api->addReport(
            $this->idSite,
            'Events',
            'table',
            ['nb_visits', 'events_with_event_value'],
            ['Resolution.Resolution'],
            '',
            '',
            '',
            '',
            false,
            'events_with_event_value',
            'asc'
        );

        $table = $this->requestReportData($idMyReport);

        // Only the visit with 1920x1080 has an event.
        $this->assertSame(['800x600', '1920x1080'], $table->getColumn('label'));
        $this->assertEquals([0, 1], $table->getColumn('events_with_event_value'));
    }

    public function testAReportWithoutDimensionCanBeComparedWithAnotherDay()
    {
        TrackedVisits::track($this->idSite);
        $idMyReport = $this->api->addReport($this->idSite, 'Visits', 'evolution', ['nb_visits']);

        $table = $this->requestReportData($idMyReport, [
            'compare' => 1,
            'compareDates' => [TrackedVisits::NEXT_DAY],
            'comparePeriods' => ['day'],
        ]);

        $this->assertEquals([2, 1], $table->getFirstRow()->getComparisons()->getColumn('nb_visits'));
    }

    public function testCoreKnowsTheDimensionOfEachLevel()
    {
        TrackedVisits::track($this->idSite);
        $idMyReport = $this->api->addReport($this->idSite, 'Pages', 'table', ['nb_visits'], ['Resolution.Resolution', 'Actions.PageUrl']);
        // Core finds the report of the request by this parameter.
        $_GET['idMyReport'] = $idMyReport;

        $flat = $this->requestReportData($idMyReport, ['flat' => 1]);
        $pivoted = $this->requestReportData($idMyReport, ['pivotBy' => 'Actions.PageUrl', 'pivotByColumn' => 'nb_visits']);

        $this->assertEqualsCanonicalizing(['example.org/a', 'example.org/b', 'example.org/a'], $flat->getRowsMetadata('Actions_PageUrl'));
        // The label and one column for each of the two pages.
        $this->assertCount(3, $pivoted->getFirstRow()->getColumns());
    }

    public function testANewReportIsWaitingToBeArchived()
    {
        $idMyReport = $this->addTableReport();

        $status = $this->api->getArchiveStatus($this->idSite);

        $this->assertSame(
            [['idreport' => $idMyReport, 'first_date' => null, 'last_date' => null, 'is_queued' => true, 'queued_days' => $status[0]['queued_days'], 'problem' => null]],
            $status
        );
        // Matomo re-archives a new report several months back.
        $this->assertGreaterThan(28, $status[0]['queued_days']);
    }

    public function testInvalidatingMarksTheDaysOfThePeriodForTheChosenReports()
    {
        $chosen = $this->addTableReport();
        $other = $this->api->addReport($this->idSite, 'Visits', 'evolution', ['nb_visits']);
        $paused = $this->api->addReport($this->idSite, 'Pageviews', 'evolution', ['pageviews']);
        $this->api->pauseReport($this->idSite, $paused);

        $invalidated = $this->api->invalidateReports('week', TrackedVisits::DAY, [$chosen, $paused]);

        // 2024-03-05 is a Tuesday.
        $this->assertSame(1, $invalidated);
        $this->assertSame(
            ['2024-03-04', '2024-03-05', '2024-03-06', '2024-03-07', '2024-03-08', '2024-03-09', '2024-03-10'],
            $this->getInvalidatedDays($chosen)
        );
        $this->assertSame([], $this->getInvalidatedDays($other));
        $this->assertSame([], $this->getInvalidatedDays($paused));
    }

    public function testInvalidatingWithoutReportsTakesAllAndNeedsSuperUserAccess()
    {
        $first = $this->addTableReport();
        $second = $this->api->addReport($this->idSite, 'Visits', 'evolution', ['nb_visits']);

        $this->assertSame(2, $this->api->invalidateReports('range', '2024-03-05,2024-03-06'));
        $this->assertSame(['2024-03-05', '2024-03-06'], $this->getInvalidatedDays($first));
        $this->assertSame(['2024-03-05', '2024-03-06'], $this->getInvalidatedDays($second));

        FakeAccess::clearAccess(false);
        FakeAccess::$idSitesAdmin = [$this->idSite];
        $this->expectException(NoAccessException::class);
        $this->api->invalidateReports('day', TrackedVisits::DAY);
    }

    public function testInvalidatingCoversAllVisitsAndTheArchivedSegmentsOrOnlyTheChosenOne()
    {
        $definition = 'resolution==800x600';
        // A segment is only archived ahead of time when archiving is not triggered by the browser.
        Rules::setBrowserTriggerArchiving(false);
        SegmentEditorApi::getInstance()->add('Small screens', $definition, $this->idSite, $autoArchive = true, true);
        Cache::getTransientCache()->flushAll();
        $everything = $this->addTableReport();
        $segmentOnly = $this->api->addReport($this->idSite, 'Visits', 'evolution', ['nb_visits']);

        $this->api->invalidateReports('day', TrackedVisits::DAY, [$everything]);
        $this->api->invalidateReports('day', TrackedVisits::DAY, [$segmentOnly], $definition);
        $notArchived = $this->api->invalidateReports('day', TrackedVisits::DAY, [$segmentOnly], 'resolution==1x1');
        Rules::setBrowserTriggerArchiving(true);

        $ofSegment = 'done' . (new Segment($definition, [$this->idSite]))->getHash() . '.RebelMyReports';
        $this->assertEqualsCanonicalizing(['done.RebelMyReports', $ofSegment], $this->getInvalidatedArchives($everything));
        $this->assertSame([$ofSegment], $this->getInvalidatedArchives($segmentOnly));
        $this->assertSame(0, $notArchived);
    }

    public function testAPausedReportKeepsItsDataAndGetsNoNewData()
    {
        TrackedVisits::track($this->idSite);
        $idMyReport = $this->addTableReport();
        $this->assertSame(2, $this->requestReportData($idMyReport)->getRowsCount());

        $this->api->pauseReport($this->idSite, $idMyReport);

        $this->assertTrue($this->api->getReport($this->idSite, $idMyReport)['paused']);
        $this->assertSame(2, $this->requestReportData($idMyReport)->getRowsCount());
        $this->assertSame(0, $this->requestReportData($idMyReport, ['date' => TrackedVisits::NEXT_DAY])->getRowsCount());
        $this->assertSame([], $this->getScheduledRecords());

        $this->api->resumeReport($this->idSite, $idMyReport);

        $this->assertFalse($this->api->getReport($this->idSite, $idMyReport)['paused']);
        $this->assertCount(1, $this->getScheduledRecords());
    }

    public function testOnlyUsersWithWriteAccessSeeWhoCreatedAndChangedAReport()
    {
        $idMyReport = $this->addTableReport();

        $report = $this->api->getReport($this->idSite, $idMyReport);
        $this->assertNotSame('', $report['created_by']);
        $this->assertSame($report['created_by'], $report['updated_by']);

        FakeAccess::clearAccess(false, [], [$this->idSite]);

        $this->assertArrayNotHasKey('created_by', $this->api->getReports($this->idSite)[0]);
    }

    public function testReportsAreListedInTheOrderTheyWereGiven()
    {
        $first = $this->addTableReport();
        $second = $this->addTableReport();
        $third = $this->addTableReport();

        $this->api->setReportOrder($this->idSite, [$third, $first, $second]);
        $new = $this->addTableReport();

        $this->assertSame([$third, $first, $second, $new], array_column($this->api->getReports($this->idSite), 'idreport'));
    }

    public function testTheOrderMustNameEachReportOfTheWebsite()
    {
        $first = $this->addTableReport();
        $this->addTableReport();

        $this->expectExceptionMessage('each report');
        $this->api->setReportOrder($this->idSite, [$first, $first]);
    }

    public function testOnlyASuperUserCanMoveAReportForAllWebsites()
    {
        $forAll = $this->api->addReport($this->idSite, 'Visits', 'evolution', ['nb_visits'], [], '', '', '', '', true);
        $first = $this->addTableReport();
        $second = $this->addTableReport();

        FakeAccess::clearAccess(false, [], [], 'writer', [$this->idSite]);
        $this->api->setReportOrder($this->idSite, [$forAll, $second, $first]);
        $this->assertSame([$forAll, $second, $first], array_column($this->api->getReports($this->idSite), 'idreport'));

        $this->expectExceptionMessage('super user');
        $this->api->setReportOrder($this->idSite, [$second, $forAll, $first]);
    }

    public function testTheNumberOfDimensionsCanBeConfigured()
    {
        Config::getInstance()->RebelMyReports = ['max_dimensions' => 1];

        $this->expectExceptionMessage('at most 1 dimensions');
        $this->api->addReport($this->idSite, 'Pages', 'table', ['nb_visits'], ['Resolution.Resolution', 'Actions.PageUrl']);
    }

    public function testValuesOfADimensionAreCountedAsOneGroup()
    {
        TrackedVisits::track($this->idSite);
        $rules = [
            ['dimension' => 'Resolution.Resolution', 'name' => 'Large & wide', 'operator' => 'equals', 'values' => ['1920x1080', '4K']],
            ['dimension' => 'Actions.PageUrl', 'name' => 'All pages', 'operator' => 'ends', 'values' => ['/a', '/b']],
        ];
        $idMyReport = $this->addGroupedReport($rules);

        $table = $this->requestReportData($idMyReport, ['expanded' => 1]);
        $large = $table->getRowFromLabel('Large &amp; wide');
        $pages = $large->getSubtable()->getFirstRow();

        $this->assertEqualsCanonicalizing(['Large &amp; wide', '800x600'], $table->getColumn('label'));
        // The visitor saw /a twice and /b once in one visit.
        $this->assertSame(['All pages'], $large->getSubtable()->getColumn('label'));
        $this->assertEquals(1, $pages->getColumn('nb_visits'));
        $this->assertSame('resolution==1920x1080,resolution==4K;pageUrl=$%2Fa,pageUrl=$%2Fb', $pages->getMetadata('segment'));
    }

    public function testGroupingRulesAreValidatedAndChangingThemBuildsTheDataAgain()
    {
        $rule = ['dimension' => 'Resolution.Resolution', 'name' => 'Small', 'operator' => 'ends', 'values' => ['x600']];
        $idMyReport = $this->addGroupedReport([$rule]);
        $revision = $this->api->getReport($this->idSite, $idMyReport)['revision'];

        $this->api->updateReport(
            $this->idSite,
            $idMyReport,
            'Grouped',
            'table',
            ['nb_visits'],
            ['Resolution.Resolution', 'Actions.PageUrl'],
            '',
            '',
            '',
            '',
            '',
            'desc',
            0,
            [],
            [['name' => 'Tiny'] + $rule]
        );
        $report = $this->api->getReport($this->idSite, $idMyReport);

        $this->assertSame([['dimension' => 'Resolution.Resolution', 'name' => 'Tiny', 'operator' => 'ends', 'values' => ['x600']]], $report['value_groups']);
        $this->assertGreaterThan($revision, $report['revision']);

        $this->expectExceptionMessage('A value group needs');
        $this->addGroupedReport([['dimension' => 'UserCountry.Country'] + $rule]);
    }

    public function testTheVisualizationIsStoredForTablesOnlyAndValidated()
    {
        $table = $this->addWithVisualization('table', ['Resolution.Resolution'], 'graphPie');
        $evolution = $this->addWithVisualization('evolution', [], 'graphPie');

        $this->assertSame('graphPie', $this->api->getReport($this->idSite, $table)['visualization']);
        $this->assertSame('table', $this->api->getReport($this->idSite, $evolution)['visualization']);

        $this->expectExceptionMessage('The visualization must be one of');
        $this->addWithVisualization('table', ['Resolution.Resolution'], 'sparklines');
    }

    private function addWithVisualization(string $type, array $dimensions, string $visualization): int
    {
        return $this->api->addReport(
            $this->idSite,
            'Report',
            $type,
            ['nb_visits'],
            $dimensions,
            '',
            '',
            '',
            '',
            false,
            '',
            'desc',
            0,
            [],
            [],
            $visualization
        );
    }

    public function testEveryExampleCanBeCreatedAndACalculatedMetricIsNotAddedTwice()
    {
        foreach (array_keys(Examples::DEFINITIONS) as $example) {
            $this->api->addExampleReport($this->idSite, $example);
        }
        $this->api->addExampleReport($this->idSite, 'Channels');

        $reports = $this->api->getReports($this->idSite);
        $metrics = $this->api->getCalculatedMetrics($this->idSite);

        $this->assertCount(count(Examples::DEFINITIONS) + 1, $reports);
        $this->assertSame(['pageviews / nb_visits'], array_column($metrics, 'formula'));
        $this->assertSame(['nb_visits', 'pageviews', 'rebelcalc_' . $metrics[0]['idmetric']], end($reports)['metrics']);
        $this->assertSame('Entry pages that lose visitors', $reports[1]['name']);
        $this->assertCount(2, $reports[1]['metric_filters']);
    }

    public function testALimitIsASystemSettingThatTheConfigFileOverrides()
    {
        $setting = StaticContainer::get(SystemSettings::class)->getSetting(Configuration::MAX_DIMENSIONS);
        $setting->setValue(2);
        $fromSetting = (new Configuration())->getMaxDimensions();

        Config::getInstance()->RebelMyReports = [Configuration::MAX_DIMENSIONS => 1];

        $this->assertSame(2, $fromSetting);
        $this->assertSame(1, (new Configuration())->getMaxDimensions());
        $this->assertFalse($setting->isWritableByCurrentUser());
    }

    /**
     * @return string[] the records core will archive for past periods on its next run
     */
    private function getScheduledRecords(): array
    {
        $entries = array_map(fn ($entry) => json_decode($entry, true), (new ReArchiveList())->getAll());

        return array_values(array_column($entries, 'report'));
    }

    /**
     * @return string[] the days waiting to be archived again for the report
     */
    private function getInvalidatedDays(int $idMyReport): array
    {
        return $this->getInvalidations($idMyReport, 'date1');
    }

    /**
     * @return string[] the archives, of all visits or a segment, waiting to be built again
     */
    private function getInvalidatedArchives(int $idMyReport): array
    {
        return $this->getInvalidations($idMyReport, 'name');
    }

    private function getInvalidations(int $idMyReport, string $column): array
    {
        $record = Archiver::getRecordName($this->api->getReport($this->idSite, $idMyReport));

        $rows = Db::fetchAll(
            "SELECT DISTINCT $column FROM `" . Common::prefixTable('archive_invalidations') . "` WHERE report = ? AND period = 1 ORDER BY $column",
            [$record]
        );

        return array_column($rows, $column);
    }

    private function addGroupedReport(array $valueGroups): int
    {
        return $this->api->addReport(
            $this->idSite,
            'Grouped',
            'table',
            ['nb_visits'],
            ['Resolution.Resolution', 'Actions.PageUrl'],
            '',
            '',
            '',
            '',
            false,
            '',
            'desc',
            0,
            [],
            $valueGroups
        );
    }

    private function addTableReport(): int
    {
        return $this->api->addReport($this->idSite, 'Resolutions', 'table', ['nb_visits'], ['Resolution.Resolution']);
    }

    private function addSortedReport(
        string $sortMetric,
        string $sortOrder = 'desc',
        int $rowLimit = 0,
        array $metricFilters = []
    ): int {
        return $this->api->addReport(
            $this->idSite,
            'Pages',
            'table',
            ['nb_visits', 'bounce_rate'],
            ['Actions.PageUrl'],
            '',
            '',
            '',
            '',
            false,
            $sortMetric,
            $sortOrder,
            $rowLimit,
            $metricFilters
        );
    }

    private function requestReportData(int $idMyReport, array $params = [])
    {
        Cache::getTransientCache()->flushAll();

        return Request::processRequest('RebelMyReports.getReportData', $params + [
            'idSite' => $this->idSite,
            'period' => 'day',
            'date' => TrackedVisits::DAY,
            'idMyReport' => $idMyReport,
        ]);
    }
}
