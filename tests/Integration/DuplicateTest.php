<?php

namespace Piwik\Plugins\RebelMyReports\tests\Integration;

use Piwik\NoAccessException;
use Piwik\Plugins\CustomDimensions\API as CustomDimensionsApi;
use Piwik\Plugins\Goals\API as GoalsApi;
use Piwik\Plugins\RebelMyReports\API;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\Mock\FakeAccess;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group RebelMyReports
 * @group Plugins
 */
class DuplicateTest extends IntegrationTestCase
{
    private API $api;

    public function setUp(): void
    {
        parent::setUp();

        Fixture::loadAllTranslations();
        FakeAccess::clearAccess(true);

        Fixture::createWebsite('2024-01-01 00:00:00');
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

    public function testReportIsCopiedWithinItsWebsite()
    {
        $idMetric = $this->api->addCalculatedMetric(1, 'Pages per visit', 'pageviews / nb_visits');
        $original = $this->api->addReport(
            1,
            'Resolutions',
            'table',
            ['nb_visits', "rebelcalc_$idMetric"],
            ['Resolution.Resolution'],
            'About screens',
            'visitCount>1',
            'General_Visitors',
            'General_Overview'
        );

        $copy = $this->api->getReport(1, $this->api->duplicateReport(1, $original));

        $this->assertSame('Resolutions (copy)', $copy['name']);
        $this->assertSame('About screens', $copy['description']);
        $this->assertSame(['nb_visits', "rebelcalc_$idMetric"], $copy['metrics']);
        $this->assertSame(['Resolution.Resolution'], $copy['dimensions']);
        $this->assertSame('visitCount>1', $copy['segment_filter']);
        $this->assertSame('General_Overview', $copy['subcategory_id']);
        $this->assertSame(1, $copy['revision']);
        $this->assertCount(1, $this->api->getCalculatedMetrics(1), 'the metric is not copied within a website');
    }

    public function testReportCopiedToAnotherWebsiteTakesItsCalculatedMetricsAlong()
    {
        $idMetric = $this->api->addCalculatedMetric(1, 'Pages per visit', 'pageviews / nb_visits', 'number');
        $original = $this->api->addReport(1, 'Resolutions', 'table', ['nb_visits', "rebelcalc_$idMetric"], ['Resolution.Resolution']);

        $copy = $this->api->getReport(2, $this->api->duplicateReport(1, $original, 2));
        $copiedMetrics = $this->api->getCalculatedMetrics(2);

        $this->assertSame('Resolutions', $copy['name']);
        $this->assertSame(2, $copy['idsite']);
        $this->assertCount(1, $copiedMetrics);
        $this->assertSame('pageviews / nb_visits', $copiedMetrics[0]['formula']);
        $this->assertSame(['nb_visits', $copiedMetrics[0]['id']], $copy['metrics']);
        $this->assertNotSame("rebelcalc_$idMetric", $copiedMetrics[0]['id']);
    }

    public function testACalculatedMetricTheOtherWebsiteAlreadyHasIsReused()
    {
        $idMetric = $this->api->addCalculatedMetric(1, 'Pages per visit', 'pageviews / nb_visits');
        $first = $this->api->addReport(1, 'First', 'evolution', ["rebelcalc_$idMetric"]);
        $second = $this->api->addReport(1, 'Second', 'evolution', ["rebelcalc_$idMetric"]);

        $this->api->duplicateReport(1, $first, 2);
        $this->api->duplicateReport(1, $second, 2);

        $this->assertCount(1, $this->api->getCalculatedMetrics(2));
        $this->assertCount(2, $this->api->getReports(2));
    }

    public function testReportGetsItsOwnPageWhenTheOtherWebsiteDoesNotHaveThePage()
    {
        $first = $this->api->addReport(1, 'First', 'evolution', ['nb_visits']);
        $pageOfFirst = $this->api->getReport(1, $first)['subcategory_id'];
        \Piwik\Cache::getTransientCache()->flushAll();
        $second = $this->api->addReport(1, 'Second', 'evolution', ['hits'], [], '', '', '', $pageOfFirst);

        $copy = $this->api->getReport(2, $this->api->duplicateReport(1, $second, 2));

        $this->assertSame('', $copy['subcategory']);
    }

    public function testReportUsingSomethingTheOtherWebsiteLacksIsNotCopied()
    {
        CustomDimensionsApi::getInstance()->configureNewCustomDimension(1, 'Colour', 'visit', 1);
        \Piwik\Cache::getTransientCache()->flushAll();
        $withDimension = $this->api->addReport(1, 'Colours', 'table', ['nb_visits'], ['CustomDimension.CustomDimension1']);
        $withGoal = $this->api->addReport(1, 'Signups', 'evolution', ['goal_1_conversion']);

        foreach ([[$withDimension, 'Unknown dimension'], [$withGoal, 'Unknown metric']] as [$idMyReport, $message]) {
            try {
                $this->api->duplicateReport(1, $idMyReport, 2);
                $this->fail('The report was copied');
            } catch (\Exception $e) {
                $this->assertStringContainsString($message, $e->getMessage());
            }
        }

        $this->assertSame([], $this->api->getReports(2));
    }

    public function testReportForAllWebsitesIsCopiedAsAReportOfOneWebsite()
    {
        $forAll = $this->api->addReport(1, 'Visits', 'evolution', ['nb_visits'], [], '', '', '', '', true);

        $copy = $this->api->getReport(2, $this->api->duplicateReport(2, $forAll, 2));

        $this->assertSame(2, $copy['idsite']);
    }

    public function testCalculatedMetricIsCopiedWithinAndBetweenWebsites()
    {
        $idMetric = $this->api->addCalculatedMetric(1, 'Bounce share', 'bounce_count / nb_visits', 'percent');

        $this->api->duplicateCalculatedMetric(1, $idMetric);
        $this->api->duplicateCalculatedMetric(1, $idMetric, 2);

        $this->assertSame(['Bounce share', 'Bounce share (copy)'], array_column($this->api->getCalculatedMetrics(1), 'name'));
        $this->assertSame(['Bounce share'], array_column($this->api->getCalculatedMetrics(2), 'name'));
        $this->assertSame('percent', $this->api->getCalculatedMetrics(2)[0]['format']);
    }

    public function testCalculatedMetricUsingAGoalOfItsWebsiteIsNotCopiedToAnother()
    {
        $idMetric = $this->api->addCalculatedMetric(1, 'Signup share', 'goal_1_conversion / nb_visits', 'percent');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('"goal_1_conversion"');

        $this->api->duplicateCalculatedMetric(1, $idMetric, 2);
    }

    public function testLongNameStillFitsAfterCopying()
    {
        $original = $this->api->addReport(1, str_repeat('x', 100), 'evolution', ['nb_visits']);

        $copy = $this->api->getReport(1, $this->api->duplicateReport(1, $original));

        $this->assertSame(100, mb_strlen($copy['name']));
        $this->assertStringEndsWith(' (copy)', $copy['name']);
    }

    public function testCopyingNeedsWriteAccessToTheDestinationOnly()
    {
        $original = $this->api->addReport(1, 'Visits', 'evolution', ['nb_visits']);

        FakeAccess::clearAccess(false, [], [1], 'user', [2]);
        $this->assertSame(2, $this->api->getReport(2, $this->api->duplicateReport(1, $original, 2))['idsite']);

        $this->expectException(NoAccessException::class);
        $this->api->duplicateReport(1, $original);
    }
}
