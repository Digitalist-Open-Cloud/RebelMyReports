<?php

namespace Piwik\Plugins\RebelMyReports\tests\Integration;

use Piwik\Archive;
use Piwik\ArchiveProcessor\Parameters;
use Piwik\Cache;
use Piwik\Config;
use Piwik\Container\StaticContainer;
use Piwik\DataAccess\LogAggregator;
use Piwik\DataTable;
use Piwik\Db;
use Piwik\Period\Factory as PeriodFactory;
use Piwik\Plugins\Goals\API as GoalsApi;
use Piwik\Plugins\RebelMyReports\Archiver;
use Piwik\Plugins\RebelMyReports\Archiving\ArchiveProblems;
use Piwik\Plugins\RebelMyReports\Archiving\ArchiveStatus;
use Piwik\Plugins\RebelMyReports\Archiving\QueryBuilder;
use Piwik\Plugins\RebelMyReports\Archiving\ReportTableBuilder;
use Piwik\Plugins\RebelMyReports\Catalogue;
use Piwik\Plugins\RebelMyReports\DataTable\Filter\FormatDimensionLabels;
use Piwik\Plugins\RebelMyReports\Model;
use Piwik\Plugins\RebelMyReports\ReportManager;
use Piwik\Plugins\RebelMyReports\tests\Fixtures\TrackedVisits;
use Piwik\Plugins\SitesManager\API as SitesManagerApi;
use Piwik\Segment;
use Piwik\Site;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

use function Piwik\Plugins\VisitTime\getTimeLabel;

/**
 * @group RebelMyReports
 * @group Plugins
 */
class ArchivingTest extends IntegrationTestCase
{
    private int $idSite;
    private Model $model;

    protected static function configureFixture($fixture)
    {
        parent::configureFixture($fixture);

        $fixture->createSuperUser = true;
        $fixture->extraPluginsToLoad = [
            'AdvertisingConversionExport',
            'FormAnalytics',
            'MarketingCampaignsReporting',
            'MediaAnalytics',
        ];
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->idSite = Fixture::createWebsite('2024-01-01 00:00:00', $ecommerce = 1);
        GoalsApi::getInstance()->addGoal($this->idSite, 'Signup', 'url', '/signup', 'contains');
        $_GET['idSite'] = $this->idSite;

        $this->model = new Model();
        TrackedVisits::track($this->idSite);
    }

    public function tearDown(): void
    {
        unset($_GET['idSite'], $_GET['period'], $_GET['date']);
        parent::tearDown();
    }

    public function testVisitMetricsByActionDimensionCountEachVisitOnce()
    {
        $table = $this->archive(['Actions.PageUrl'], ['nb_visits', 'pageviews', 'sum_actions']);

        // Visitor 1 has 4 actions and views /a twice, visitor 2 has 1 action.
        $this->assertEquals(
            ['nb_visits' => 2, 'pageviews' => 3, 'sum_actions' => 5],
            $this->columns($table, 'example.org/a')
        );
        $this->assertEquals(
            ['nb_visits' => 1, 'pageviews' => 1, 'sum_actions' => 4],
            $this->columns($table, 'example.org/b')
        );
    }

    public function testActionMetricsByVisitDimension()
    {
        $table = $this->archive(['Resolution.Resolution'], ['hits', 'nb_uniq_actions_pageurl', 'sum_events_eventvalue']);

        $this->assertEquals(
            ['hits' => 4, 'nb_uniq_actions_pageurl' => 2, 'sum_events_eventvalue' => 5],
            $this->columns($table, '1920x1080')
        );
        $this->assertEquals(
            ['hits' => 1, 'nb_uniq_actions_pageurl' => 1],
            $this->columns($table, '800x600')
        );
    }

    public function testFurtherDimensionsBecomeSubtables()
    {
        $table = $this->archive(['Resolution.Resolution', 'Actions.PageUrl'], ['nb_visits', 'pageviews']);

        $row = $table->getRowFromLabel('1920x1080');
        $this->assertEquals(['nb_visits' => 1, 'pageviews' => 3], $this->columns($table, '1920x1080'));
        $this->assertEquals(['nb_visits' => 1, 'pageviews' => 2], $this->columns($row->getSubtable(), 'example.org/a'));
        $this->assertEquals(['nb_visits' => 1, 'pageviews' => 1], $this->columns($row->getSubtable(), 'example.org/b'));
    }

    public function testReportWithoutDimensionHasOneRow()
    {
        $table = $this->archive([], ['nb_visits', 'nb_uniq_visitors', 'bounce_rate']);

        $this->assertEquals(1, $table->getRowsCount());
        $this->assertEquals(
            ['nb_visits' => 2, 'nb_uniq_visitors' => 2, 'bounce_count' => 1],
            $this->columns($table, 'total')
        );
    }

    public function testWeekCountsUniqueValuesOverTheWholeWeek()
    {
        $metrics = ['nb_uniq_visitors', 'nb_visits', 'max_actions'];

        $table = $this->archive([], $metrics, 'week');

        // Visitor 1 returns the next day: 3 visits by 2 visitors.
        $this->assertEquals(
            ['nb_uniq_visitors' => 2, 'nb_visits' => 3, 'max_actions' => 4],
            $this->columns($table, 'total')
        );
    }

    public function testWeekOfAdditiveMetricsIsAggregatedFromDays()
    {
        $table = $this->archive(['Actions.PageUrl'], ['nb_visits', 'max_actions'], 'week');

        $this->assertEquals(
            ['nb_visits' => 2, 'max_actions' => 4],
            $this->columns($table, 'example.org/b')
        );
    }

    public function testHoursAreShownInTheTimezoneOfTheWebsite()
    {
        SitesManagerApi::getInstance()->updateSite($this->idSite, null, null, null, null, null, null, null, null, 'Europe/Stockholm');
        $_GET['period'] = 'day';
        $_GET['date'] = TrackedVisits::DAY;
        $id = 'CoreHome.VisitLastActionTime';

        $table = $this->archive([$id], ['nb_visits']);
        $dimensions = [StaticContainer::get(Catalogue::class)->getDimensions()[$id]];
        $table->filter(FormatDimensionLabels::class, [$dimensions, $this->idSite]);

        // Tracked at 10 and 11 UTC. Core's own reports show the same hours. Both hours have one
        // visit, so the order of the rows is not fixed.
        $this->assertEqualsCanonicalizing([getTimeLabel(11), getTimeLabel(12)], $table->getColumn('label'));
    }

    public function testArchiveStatusTellsWhichDaysAReportHasDataFor()
    {
        $archived = $this->addReport([], ['nb_visits']);
        $other = $this->addReport([], ['nb_actions']);
        Cache::getTransientCache()->flushAll();
        $record = Archiver::getRecordName($this->model->getReport($archived));
        Archive::build($this->idSite, 'day', TrackedVisits::DAY)->getDataTable($record);
        Archive::build($this->idSite, 'day', TrackedVisits::NEXT_DAY)->getDataTable($record);

        $status = (new ArchiveStatus())->get($this->model->getReports($this->idSite), $this->idSite);

        $this->assertSame(TrackedVisits::DAY, $status[$archived]['first_date']);
        $this->assertSame(TrackedVisits::NEXT_DAY, $status[$archived]['last_date']);
        $this->assertNull($status[$other]['first_date']);
        $this->assertFalse($status[$archived]['is_queued']);
    }

    public function testTheNumberOfArchivedRowsCanBeConfigured()
    {
        Config::getInstance()->RebelMyReports = ['datatable_archiving_maximum_rows' => 1];

        $table = $this->archive(['Actions.PageUrl'], ['nb_visits']);

        // Both pages are summed into the row for the rest.
        $this->assertSame(1, $table->getRowsCount());
        $this->assertEquals(3, $table->getFirstRow()->getColumn('nb_visits'));
    }

    public function testSegmentFilterOfTheReportIsApplied()
    {
        $table = $this->archive(['Actions.PageUrl'], ['nb_visits'], 'day', 'resolution==800x600');

        $this->assertEquals(['nb_visits' => 1], $this->columns($table, 'example.org/a'));
        $this->assertFalse($table->getRowFromLabel('example.org/b'));
    }

    public function testGoalMetricsOnlyCountTheirGoal()
    {
        $table = $this->archive(['Resolution.Resolution'], ['goal_1_conversion', 'nb_uniq_orders', 'sum_ecommerce_revenue']);

        $this->assertEquals(
            ['goal_1_conversion' => 1],
            $this->columns($table, '800x600')
        );
        $this->assertEquals(
            ['nb_uniq_orders' => 1, 'sum_ecommerce_revenue' => 30],
            $this->columns($table, '1920x1080')
        );
    }

    public function testABrokenReportDoesNotStopArchiving()
    {
        $this->addReport(['Actions.PageUrl'], ['metric_that_was_removed']);

        $table = $this->archive([], ['nb_visits']);

        $this->assertEquals(['nb_visits' => 2], $this->columns($table, 'total'));
    }

    public function testAReportThatCannotBeArchivedIsRememberedUntilItsDataChanges()
    {
        $idMyReport = $this->addReport(['Actions.PageUrl'], ['metric_that_was_removed']);
        $this->archive([], ['nb_visits']);

        $status = (new ArchiveStatus())->get($this->model->getReports($this->idSite), $this->idSite);
        $this->assertSame(
            ['type' => ArchiveProblems::FAILED, 'period' => TrackedVisits::DAY . ',' . TrackedVisits::DAY],
            $status[$idMyReport]['problem']
        );

        StaticContainer::get(ReportManager::class)->rebuildReport($this->model->getReport($idMyReport));

        $status = (new ArchiveStatus())->get($this->model->getReports($this->idSite), $this->idSite);
        $this->assertNull($status[$idMyReport]['problem']);
    }

    public function testAQueryThatRunsLongerThanAllowedIsStopped()
    {
        $builder = StaticContainer::get(ReportTableBuilder::class);
        $period = PeriodFactory::build('day', TrackedVisits::DAY);
        $params = new Parameters(new Site($this->idSite), $period, new Segment('', [$this->idSite]));
        $logAggregator = new class ($params) extends LogAggregator {
            public function getDb()
            {
                $db = parent::getDb();

                return new class ($db) {
                    private $db;

                    public function __construct($db)
                    {
                        $this->db = $db;
                    }

                    public function query($sql, $bind)
                    {
                        // Makes each row take two seconds, which the limit of one second stops.
                        return $this->db->query(preg_replace('/SELECT/', 'SELECT SLEEP(2),', $sql, 1), $bind);
                    }
                };
            }
        };

        try {
            $builder->build($logAggregator, ['Actions.PageUrl'], ['nb_visits'], 1);
            $this->fail('The query was not stopped.');
        } catch (\Exception $e) {
            $this->assertTrue((new ArchiveProblems())->isTimeout($e), $e->getMessage());
        }
    }

    public function testEveryMetricCanBeQueriedAloneAndByAnotherTablesDimension()
    {
        $catalogue = StaticContainer::get(Catalogue::class);
        $builder = StaticContainer::get(QueryBuilder::class);
        $failures = [];

        foreach (array_keys($catalogue->getMetrics()) as $name) {
            $metrics = $builder->getArchivedMetrics([$name]);

            foreach ([[], ['Resolution.Resolution'], ['Actions.PageUrl'], ['Ecommerce.ProductName']] as $dimensions) {
                $failures = array_merge($failures, $this->runQueries($builder, $dimensions, $metrics, $name));
            }
        }

        $this->assertEquals([], $failures);
    }

    public function testEveryDimensionCanBeQueriedWithVisitAndActionMetrics()
    {
        $catalogue = StaticContainer::get(Catalogue::class);
        $builder = StaticContainer::get(QueryBuilder::class);
        $metrics = $builder->getArchivedMetrics(['nb_visits', 'hits']);
        $failures = [];

        foreach (array_keys($catalogue->getDimensions()) as $id) {
            $failures = array_merge($failures, $this->runQueries($builder, [$id], $metrics, $id));
        }

        $this->assertEquals([], $failures);
    }

    /**
     * @return string[] error messages
     */
    private function runQueries(QueryBuilder $builder, array $dimensions, array $metrics, string $subject): array
    {
        try {
            foreach ($builder->buildQueries($this->logAggregator(), $dimensions, $metrics) as $query) {
                Db::fetchAll($query['sql'], $query['bind']);
            }
        } catch (\Exception $e) {
            return [$subject . ' by [' . implode(', ', $dimensions) . ']: ' . substr($e->getMessage(), 0, 300)];
        }

        return [];
    }

    private function logAggregator(): LogAggregator
    {
        $period = PeriodFactory::build('day', TrackedVisits::DAY);
        $params = new Parameters(new Site($this->idSite), $period, new Segment('', [$this->idSite]));

        return new LogAggregator($params);
    }

    private function archive(array $dimensions, array $metrics, string $period = 'day', string $segmentFilter = ''): DataTable
    {
        $idMyReport = $this->addReport($dimensions, $metrics, $segmentFilter);
        Cache::getTransientCache()->flushAll();

        $archive = Archive::build($this->idSite, $period, TrackedVisits::DAY);

        return $archive->getDataTableExpanded(Archiver::getRecordName($this->model->getReport($idMyReport)));
    }

    private function addReport(array $dimensions, array $metrics, string $segmentFilter = ''): int
    {
        return $this->model->addReport([
            'idsite' => $this->idSite,
            'name' => 'Test',
            'report_type' => Model::TYPE_TABLE,
            'dimensions' => $dimensions,
            'metrics' => $metrics,
            'segment_filter' => $segmentFilter,
        ]);
    }

    private function columns(DataTable $table, string $label): array
    {
        $row = $table->getRowFromLabel($label);
        $this->assertNotFalse($row, "No row '$label', got: " . implode(', ', $table->getColumn('label')));

        return array_map('intval', array_diff_key($row->getColumns(), ['label' => 0]));
    }
}
