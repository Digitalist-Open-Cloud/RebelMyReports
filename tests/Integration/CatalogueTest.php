<?php

namespace Piwik\Plugins\RebelMyReports\tests\Integration;

use Piwik\Container\StaticContainer;
use Piwik\Plugin\Manager;
use Piwik\Plugins\CustomDimensions\API as CustomDimensionsApi;
use Piwik\Plugins\Goals\API as GoalsApi;
use Piwik\Plugins\RebelMyReports\Catalogue;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group RebelMyReports
 * @group Plugins
 */
class CatalogueTest extends IntegrationTestCase
{
    // Required names that Matomo exposes under another label, mapped to the catalogue ID.
    private const DIMENSION_ALIASES = [
        'website name' => 'CoreHome.IdSite',
    ];

    private const METRIC_ALIASES = [
        'order id' => 'nb_uniq_orders',
        'conversions goal' => 'goal_1_conversion',
        'revenue goal' => 'goal_1_revenue',
        'days to conversion goal' => 'goal_1_daystoconversion',
        'visits to conversion goal' => 'goal_1_visitstoconversion',
        'goal conversion rate' => 'goal_1_conversion_uniq_visitors_rate',
    ];

    // Not part of Matomo. What they add is only required where they are installed.
    private const PREMIUM_PLUGINS = ['AdvertisingConversionExport', 'FormAnalytics', 'MediaAnalytics'];

    private Catalogue $catalogue;

    protected static function configureFixture($fixture)
    {
        parent::configureFixture($fixture);

        $fixture->extraPluginsToLoad = array_merge(['MarketingCampaignsReporting'], self::PREMIUM_PLUGINS);
    }

    public function setUp(): void
    {
        parent::setUp();

        Fixture::loadAllTranslations();

        $idSite = Fixture::createWebsite('2024-01-01 00:00:00', $ecommerce = 1);
        GoalsApi::getInstance()->addGoal($idSite, 'Signup', 'url', '/signup', 'contains');

        foreach (['Location', 'Referrer', 'Utm_custom'] as $name) {
            CustomDimensionsApi::getInstance()->configureNewCustomDimension($idSite, $name, 'visit', 1);
        }
        foreach (['Colour', 'Date', 'Extract'] as $name) {
            CustomDimensionsApi::getInstance()->configureNewCustomDimension($idSite, $name, 'action', 1);
        }

        $_GET['idSite'] = $idSite;

        $this->catalogue = StaticContainer::get(Catalogue::class);
    }

    public function tearDown(): void
    {
        unset($_GET['idSite']);
        Fixture::resetTranslations();
        parent::tearDown();
    }

    public function testCoversAllRequiredDimensions()
    {
        $dimensions = $this->catalogue->getDimensions();
        $labels = [];
        foreach ($dimensions as $dimension) {
            $labels[mb_strtolower($dimension->getName())] = true;
        }

        $missing = [];
        foreach ($this->readRequired('dimensions') as $name) {
            $alias = self::DIMENSION_ALIASES[$name] ?? null;
            if (!isset($labels[$name]) && !isset($dimensions[$alias])) {
                $missing[] = $name;
            }
        }

        $this->assertSame([], $missing);
    }

    public function testCoversAllRequiredMetrics()
    {
        $metrics = $this->catalogue->getMetrics();
        $labels = [];
        foreach ($metrics as $metric) {
            $labels[mb_strtolower($this->catalogue->getMetricLabel($metric))] = true;
        }

        $missing = [];
        foreach ($this->readRequired('metrics') as $name) {
            $alias = self::METRIC_ALIASES[$name] ?? null;
            if (!isset($labels[$name]) && !isset($metrics[$alias])) {
                $missing[] = $name;
            }
        }

        $this->assertSame([], $missing);
    }

    /**
     * @dataProvider getCombinableSelections
     */
    public function testAcceptsCombinableSelections(array $dimensions, array $metrics)
    {
        if ($dimensions === ['FormAnalytics.FormName', 'MediaAnalytics.MediaTitle'] && !$this->hasPremiumPlugins()) {
            $this->markTestSkipped('Needs FormAnalytics and MediaAnalytics.');
        }

        $this->catalogue->assertCombinable($dimensions, $metrics);
        $this->addToAssertionCount(1);
    }

    public function getCombinableSelections(): array
    {
        return [
            'no dimension' => [[], ['nb_visits']],
            'same table' => [['UserCountry.Country'], ['nb_visits', 'bounce_rate']],
            'action dimension, visit metric' => [['Actions.PageUrl'], ['nb_uniq_visitors']],
            'visit dimension, action metric' => [['DevicesDetection.BrowserName'], ['hits']],
            'three tables' => [
                ['UserCountry.Country', 'Actions.PageUrl', 'Ecommerce.ProductName'],
                ['nb_visits', 'sum_ecommerce_productquantity'],
            ],
            'forms and media' => [['FormAnalytics.FormName', 'MediaAnalytics.MediaTitle'], ['nb_visits']],
        ];
    }

    /**
     * @dataProvider getInvalidSelections
     */
    public function testRejectsInvalidSelections(array $dimensions, array $metrics, string $expectedMessage)
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->catalogue->assertCombinable($dimensions, $metrics);
    }

    public function getInvalidSelections(): array
    {
        return [
            'no metric' => [['UserCountry.Country'], [], 'at least one metric'],
            'unknown dimension' => [['Nope.Nope'], ['nb_visits'], 'Unknown dimension'],
            'sql as dimension' => [['log_visit.idvisit; DROP TABLE log_visit'], ['nb_visits'], 'Unknown dimension'],
            'non-string dimension' => [[['UserCountry.Country']], ['nb_visits'], 'Unknown dimension'],
            'unknown metric' => [['UserCountry.Country'], ['count(*)'], 'Unknown metric'],
            'dimension without a column' => [['Referrers.Referrer'], ['nb_visits'], 'Unknown dimension'],
            'too many dimensions' => [
                ['UserCountry.Country', 'UserCountry.City', 'UserCountry.Region', 'UserCountry.Continent'],
                ['nb_visits'],
                'at most 3 dimensions',
            ],
        ];
    }

    private function hasPremiumPlugins(): bool
    {
        return array_filter(self::PREMIUM_PLUGINS, [Manager::getInstance(), 'isPluginLoaded']) === self::PREMIUM_PLUGINS;
    }

    /**
     * @return string[] lower-cased names
     */
    private function readRequired(string $kind): array
    {
        $files = ['required-' . $kind . '.txt'];
        if ($this->hasPremiumPlugins()) {
            $files[] = 'required-' . $kind . '-premium.txt';
        }

        $lines = [];
        foreach ($files as $file) {
            $path = __DIR__ . '/../resources/' . $file;
            $lines = array_merge($lines, file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));
        }

        return array_map('mb_strtolower', $lines);
    }
}
