<?php

namespace Piwik\Plugins\RebelMyReports\tests\Integration;

use Piwik\API\Request;
use Piwik\Cache;
use Piwik\NoAccessException;
use Piwik\Plugins\Goals\API as GoalsApi;
use Piwik\Plugins\RebelMyReports\API;
use Piwik\Plugins\RebelMyReports\tests\Fixtures\TrackedVisits;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\Mock\FakeAccess;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group RebelMyReports
 * @group Plugins
 */
class CalculatedMetricsTest extends IntegrationTestCase
{
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

        Fixture::createWebsite('2024-01-01 00:00:00', $ecommerce = 1);
        Fixture::createWebsite('2024-01-01 00:00:00');
        GoalsApi::getInstance()->addGoal(1, 'Signup', 'url', '/signup', 'contains');
        $_GET['idSite'] = 1;

        $this->api = API::getInstance();
    }

    public function tearDown(): void
    {
        unset($_GET['idSite']);
        Fixture::resetTranslations();
        parent::tearDown();
    }

    public function provideContainerConfig()
    {
        return ['Piwik\Access' => new FakeAccess()];
    }

    public function testCalculatedMetricIsListedWithTheOtherMetrics()
    {
        $idMetric = $this->api->addCalculatedMetric(1, 'Pages per visit', ' pageviews / nb_visits ');

        $stored = $this->api->getCalculatedMetrics(1);
        $available = array_column($this->api->getAvailableMetrics(1), null, 'id');

        $this->assertSame('pageviews / nb_visits', $stored[0]['formula']);
        $this->assertSame("rebelcalc_$idMetric", $stored[0]['id']);
        $this->assertSame('Pages per visit', $available["rebelcalc_$idMetric"]['name']);
        $this->assertSame('Calculated metrics', $available["rebelcalc_$idMetric"]['category']);
        $this->assertSame("rebelcalc_$idMetric", $this->api->getAvailableMetrics(1)[0]['id'], 'calculated metrics are listed first');
        $this->assertSame([], $this->api->getCalculatedMetrics(2));
    }

    public function testMetricCanBeAddedAndChangedThroughAnApiRequest()
    {
        $idMetric = Request::processRequest('RebelMyReports.addCalculatedMetric', [
            'idSite' => 1,
            'name' => 'Bounce share',
            'formula' => 'bounce_count / nb_visits',
            'metricFormat' => 'percent',
            // The reply format of the API, which is not the format of the metric.
            'format' => 'original',
        ]);
        Request::processRequest('RebelMyReports.updateCalculatedMetric', [
            'idSite' => 1,
            'idMetric' => $idMetric,
            'name' => 'Bounce share',
            'formula' => 'bounce_count / nb_visits',
            'metricFormat' => 'duration',
            'format' => 'original',
        ]);

        $this->assertSame('duration', $this->api->getCalculatedMetrics(1)[0]['format']);
    }

    public function testReportShowsTheMetricCalculatedPerRow()
    {
        TrackedVisits::track(1);
        $pagesPerVisit = $this->api->addCalculatedMetric(1, 'Pages per visit', 'pageviews / nb_visits');
        $share = $this->api->addCalculatedMetric(1, 'Bounce share', 'bounce_count / nb_visits', 'percent');
        $idMyReport = $this->api->addReport(
            1,
            'Resolutions',
            'table',
            ["rebelcalc_$pagesPerVisit", "rebelcalc_$share"],
            ['Resolution.Resolution']
        );

        $table = $this->requestReportData($idMyReport);

        // Visitor 1 views 3 pages in one visit, visitor 2 one page and leaves.
        $this->assertEquals(3, $table->getRowFromLabel('1920x1080')->getColumn("rebelcalc_$pagesPerVisit"));
        $this->assertSame('0%', $table->getRowFromLabel('1920x1080')->getColumn("rebelcalc_$share"));
        $this->assertEquals(1, $table->getRowFromLabel('800x600')->getColumn("rebelcalc_$pagesPerVisit"));
        $this->assertSame('100%', $table->getRowFromLabel('800x600')->getColumn("rebelcalc_$share"));
    }

    public function testWeekIsCalculatedFromTheTotalsOfTheWeek()
    {
        TrackedVisits::track(1);
        $idMetric = $this->api->addCalculatedMetric(1, 'Pages per visit', 'pageviews / nb_visits');
        $idMyReport = $this->api->addReport(1, 'Pages per visit', 'evolution', ["rebelcalc_$idMetric"]);

        $table = $this->requestReportData($idMyReport, ['period' => 'week']);

        // 5 page views in 3 visits. Averaging the days (2 and 1) would give 1.5.
        $this->assertSame('1.67', $table->getFirstRow()->getColumn("rebelcalc_$idMetric"));
    }

    public function testPreviewShowsCalculatedMetrics()
    {
        TrackedVisits::track(1);
        $idMetric = $this->api->addCalculatedMetric(1, 'Pages per visit', 'pageviews / nb_visits');

        $table = Request::processRequest('RebelMyReports.previewReport', [
            'idSite' => 1,
            'metrics' => ["rebelcalc_$idMetric"],
            'dimensions' => ['Resolution.Resolution'],
            'date' => TrackedVisits::DAY,
        ]);

        $this->assertEquals(3, $table->getRowFromLabel('1920x1080')->getColumn("rebelcalc_$idMetric"));
    }

    /**
     * @dataProvider getInvalidMetrics
     */
    public function testRejectsInvalidMetrics(string $name, string $formula, string $format, string $expectedMessage)
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->api->addCalculatedMetric(1, $name, $formula, $format);
    }

    public function getInvalidMetrics(): array
    {
        return [
            'no name' => [' ', 'nb_visits / 2', 'number', 'needs a name'],
            'unknown format' => ['Half', 'nb_visits / 2', 'bytes', 'format must be one of'],
            'broken formula' => ['Half', 'nb_visits /', 'number', 'formula is not valid'],
            'not arithmetic' => ['Half', 'nb_visits; DROP TABLE log_visit', 'number', 'formula is not valid'],
            'unknown metric' => ['Half', 'no_such_metric / 2', 'number', '"no_such_metric"'],
            'rate in formula' => ['Half', 'bounce_rate / 2', 'number', '"bounce_rate"'],
            'only numbers' => ['Three', '1 + 2', 'number', 'at least one metric'],
        ];
    }

    public function testFormulaCannotUseAnotherCalculatedMetric()
    {
        $idMetric = $this->api->addCalculatedMetric(1, 'Half', 'nb_visits / 2');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("\"rebelcalc_$idMetric\"");

        $this->api->addCalculatedMetric(1, 'Quarter', "rebelcalc_$idMetric / 2");
    }

    public function testChangingTheFormulaRebuildsTheReportsThatShowTheMetric()
    {
        $idMetric = $this->api->addCalculatedMetric(1, 'Half', 'nb_visits / 2');
        $using = $this->api->addReport(1, 'Uses it', 'evolution', ["rebelcalc_$idMetric"]);
        $other = $this->api->addReport(1, 'Other', 'evolution', ['nb_visits']);

        $this->api->updateCalculatedMetric(1, $idMetric, 'Renamed', 'nb_visits / 2');
        $this->assertSame(1, $this->api->getReport(1, $using)['revision'], 'a new name keeps the data');

        $this->api->updateCalculatedMetric(1, $idMetric, 'Renamed', 'pageviews / 2');

        $this->assertSame(2, $this->api->getReport(1, $using)['revision']);
        $this->assertSame(1, $this->api->getReport(1, $other)['revision']);
        $this->assertSame('Renamed', $this->api->getCalculatedMetrics(1)[0]['name']);
    }

    public function testMetricShownByAReportCannotBeDeleted()
    {
        $idMetric = $this->api->addCalculatedMetric(1, 'Half', 'nb_visits / 2');
        $idMyReport = $this->api->addReport(1, 'Uses it', 'evolution', ["rebelcalc_$idMetric"]);

        try {
            $this->api->deleteCalculatedMetric(1, $idMetric);
            $this->fail('The metric was deleted');
        } catch (\Exception $e) {
            $this->assertStringContainsString('Uses it', $e->getMessage());
        }

        $this->api->deleteReport(1, $idMyReport);
        $this->api->deleteCalculatedMetric(1, $idMetric);

        $this->assertSame([], $this->api->getCalculatedMetrics(1));
    }

    public function testCalculatedMetricCannotBeUsedInAReportForAllWebsites()
    {
        $idMetric = $this->api->addCalculatedMetric(1, 'Half', 'nb_visits / 2');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('only exists for this website');

        $this->api->addReport(1, 'For all', 'evolution', ["rebelcalc_$idMetric"], [], '', '', '', '', true);
    }

    public function testMetricOfAnotherWebsiteIsNotFound()
    {
        $idMetric = $this->api->addCalculatedMetric(1, 'Half', 'nb_visits / 2');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('no calculated metric with ID');

        $this->api->deleteCalculatedMetric(2, $idMetric);
    }

    public function testViewUserCannotAddMetrics()
    {
        FakeAccess::clearAccess(false, [], [1]);

        $this->assertSame([], $this->api->getCalculatedMetrics(1));

        $this->expectException(NoAccessException::class);
        $this->api->addCalculatedMetric(1, 'Half', 'nb_visits / 2');
    }

    private function requestReportData(int $idMyReport, array $params = [])
    {
        Cache::getTransientCache()->flushAll();

        return Request::processRequest('RebelMyReports.getReportData', $params + [
            'idSite' => 1,
            'period' => 'day',
            'date' => TrackedVisits::DAY,
            'idMyReport' => $idMyReport,
            // As the report view does. API requests leave the numbers unformatted by default.
            'format_metrics' => 1,
        ]);
    }
}
