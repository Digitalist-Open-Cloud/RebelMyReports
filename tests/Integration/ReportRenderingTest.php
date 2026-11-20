<?php

namespace Piwik\Plugins\RebelMyReports\tests\Integration;

use Piwik\Cache;
use Piwik\Date;
use Piwik\FrontController;
use Piwik\Plugins\Goals\API as GoalsApi;
use Piwik\Plugins\RebelMyReports\API;
use Piwik\Plugins\RebelMyReports\tests\Fixtures\TrackedVisits;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group RebelMyReports
 * @group Plugins
 */
class ReportRenderingTest extends IntegrationTestCase
{
    private const REQUEST = ['idSite' => 1, 'period' => 'day', 'date' => TrackedVisits::DAY];

    private array $get;

    protected static function configureFixture($fixture)
    {
        parent::configureFixture($fixture);

        $fixture->createSuperUser = true;
    }

    public function setUp(): void
    {
        parent::setUp();

        Fixture::loadAllTranslations();
        Fixture::createWebsite('2024-01-01 00:00:00', $ecommerce = 1);
        GoalsApi::getInstance()->addGoal(1, 'Signup', 'url', '/signup', 'contains');
        TrackedVisits::track(1);

        $this->get = $_GET;
        $_GET = self::REQUEST + $_GET;
    }

    public function tearDown(): void
    {
        $_GET = $this->get;
        Fixture::resetTranslations();
        parent::tearDown();
    }

    public function testTableReportRendersItsRows()
    {
        $html = $this->render(API::getInstance()->addReport(1, 'Resolutions', 'table', ['nb_visits'], ['Resolution.Resolution']));

        $this->assertStringContainsString('1920x1080', $html);
        $this->assertStringContainsString('800x600', $html);
    }

    public function testAReportThatIsStillBeingBuiltSaysSo()
    {
        $html = $this->render(API::getInstance()->addReport(1, 'Resolutions', 'table', ['nb_visits'], ['Resolution.Resolution']));

        $this->assertStringContainsString('still being built', $html);
    }

    public function testTableReportCanBeShownAsInsights()
    {
        $_GET['date'] = TrackedVisits::NEXT_DAY;
        $_GET['viewDataTable'] = 'insightsVisualization';

        $html = $this->render(API::getInstance()->addReport(1, 'Pages', 'table', ['nb_visits'], ['Actions.PageUrl']));

        // The page had two visits the day before and none on this day.
        $this->assertStringContainsString('example.org/a', $html);
        $this->assertStringContainsString('-100%', $html);
    }

    public function testEvolutionReportRendersAGraph()
    {
        $html = $this->render(API::getInstance()->addReport(1, 'Visits', 'evolution', ['nb_visits']));

        $this->assertStringContainsString('dataTableVizEvolution', $html);
    }

    public function testTableReportOpensWithTheVisualizationItIsSetTo()
    {
        $idMyReport = API::getInstance()->addReport(
            1,
            'Resolutions',
            'table',
            ['nb_visits'],
            ['Resolution.Resolution'],
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
            'graphVerticalBar'
        );

        $this->assertStringContainsString('dataTableVizBar', $this->render($idMyReport));
    }

    public function testKeyFigureRendersItsValueChangeAndTheValueOfEachPoint()
    {
        $html = $this->render(API::getInstance()->addReport(1, 'Visits', 'keyfigure', ['nb_visits']));

        $props = $this->getKeyFigureProps($html);
        $lastPoints = array_slice($props['points'], -2);

        $this->assertSame('Visits', $props['metric-name']);
        $this->assertEquals(2, $props['value']);
        $this->assertCount(30, $props['points']);
        $this->assertSame([0, 2], array_column($lastPoints, 'value'));
        $this->assertEquals(2, $props['evolution']['trend']);
    }

    public function testKeyFigureShowsThePeriodsItIsComparedWith()
    {
        $idMyReport = API::getInstance()->addReport(1, 'Visits', 'keyfigure', ['nb_visits']);
        $_GET['comparePeriods'] = ['day'];
        $_GET['compareDates'] = [Date::factory($_GET['date'])->subDay(1)->toString()];

        $comparisons = $this->getKeyFigureProps($this->render($idMyReport))['comparisons'];
        unset($_GET['comparePeriods'], $_GET['compareDates']);

        $this->assertCount(1, $comparisons);
        $this->assertSame(0, $comparisons[0]['value']);
        $this->assertEquals(2, $comparisons[0]['evolution']['trend']);
    }

    public function testKeyFigureShowsTheSegmentsItIsComparedWith()
    {
        $idMyReport = API::getInstance()->addReport(1, 'Visits', 'keyfigure', ['nb_visits']);
        $_GET['compareSegments'] = ['resolution==800x600'];

        $comparisons = $this->getKeyFigureProps($this->render($idMyReport))['comparisons'];
        unset($_GET['compareSegments']);

        $this->assertCount(1, $comparisons);
        $this->assertSame('resolution==800x600', $comparisons[0]['label']);
        $this->assertSame(1, $comparisons[0]['value']);
        $this->assertEquals(1, $comparisons[0]['evolution']['trend']);
    }

    /**
     * @return array<string, mixed> the properties passed to the key figure component
     */
    private function getKeyFigureProps(string $html): array
    {
        $this->assertStringContainsString('vue-entry="RebelMyReports.MyReportKeyFigure"', $html);

        preg_match_all('/ ([a-z-]+)="([^"]*)"/', $html, $matches, PREG_SET_ORDER);
        $props = [];
        foreach ($matches as $match) {
            $props[$match[1]] = json_decode(html_entity_decode($match[2], ENT_QUOTES), true);
        }

        return $props;
    }

    private function render(int $idMyReport): string
    {
        Cache::getTransientCache()->flushAll();
        $_GET['idMyReport'] = $idMyReport;
        $_GET['module'] = 'RebelMyReports';
        $_GET['action'] = 'getReportData';

        return (string) FrontController::getInstance()->dispatch('RebelMyReports', 'getReportData');
    }
}
