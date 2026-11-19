<?php

namespace Piwik\Plugins\RebelMyReports\tests\Integration\Commands;

use Piwik\Common;
use Piwik\Db;
use Piwik\Plugins\Goals\API as GoalsApi;
use Piwik\Plugins\RebelMyReports\Model;
use Piwik\Plugins\RebelMyReports\tests\Fixtures\TrackedVisits;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\TestCase\ConsoleCommandTestCase;

/**
 * @group RebelMyReports
 * @group Plugins
 */
class ArchiveTest extends ConsoleCommandTestCase
{
    private int $idReport;

    protected static function configureFixture($fixture)
    {
        parent::configureFixture($fixture);

        $fixture->createSuperUser = true;
    }

    public function setUp(): void
    {
        parent::setUp();

        Fixture::createWebsite('2024-01-01 00:00:00', $ecommerce = 1);
        Fixture::createWebsite('2024-01-01 00:00:00');
        GoalsApi::getInstance()->addGoal(1, 'Signup', 'url', '/signup', 'contains');

        $model = new Model();
        $model->install();
        $this->idReport = $model->addReport([
            'idsite' => 1,
            'name' => 'Resolutions',
            'report_type' => Model::TYPE_TABLE,
            'dimensions' => ['Resolution.Resolution'],
            'metrics' => ['nb_visits'],
        ]);
    }

    public function testListShowsReportsWithTheirRecordName()
    {
        $result = $this->applicationTester->run(['command' => 'rebelmyreports:list']);

        $this->assertSame(0, $result, $this->getCommandDisplayOutputErrorMessage());
        $this->assertStringContainsString('Resolutions', $this->applicationTester->getDisplay());
        $this->assertStringContainsString("RebelMyReports_{$this->idReport}_1", $this->applicationTester->getDisplay());
    }

    public function testListCanBeRestrictedToAWebsite()
    {
        $this->applicationTester->run(['command' => 'rebelmyreports:list', '--idsite' => 2]);

        $this->assertStringNotContainsString('Resolutions', $this->applicationTester->getDisplay());
    }

    /**
     * @dataProvider getInvalidOptions
     */
    public function testArchiveRejectsInvalidOptions(array $options, string $expectedMessage)
    {
        if (($options['--idmyreport'] ?? '') === 'existing') {
            $options['--idmyreport'] = (string) $this->idReport;
        }

        $result = $this->applicationTester->run(['command' => 'rebelmyreports:archive'] + $options);

        $this->assertNotSame(0, $result);
        $this->assertStringContainsString($expectedMessage, $this->applicationTester->getDisplay());
    }

    public function getInvalidOptions(): array
    {
        return [
            'unknown report' => [['--idmyreport' => '999999'], "no report with ID '999999'"],
            'not a number' => [['--idmyreport' => '1 OR 1=1'], 'no report with ID'],
            'date without rearchive' => [['--idmyreport' => 'existing', '--date' => '2024-03-05'], 'need --rearchive'],
            'website of another report' => [['--idmyreport' => 'existing', '--idsite' => '2'], 'belongs to website 1'],
        ];
    }

    public function testWithoutOptionsAllReportsAreArchived()
    {
        $model = new Model();
        $second = $model->addReport([
            'idsite' => 2,
            'name' => 'Visits',
            'report_type' => Model::TYPE_EVOLUTION,
            'dimensions' => [],
            'metrics' => ['nb_visits'],
        ]);

        $result = $this->applicationTester->run(['command' => 'rebelmyreports:archive']);

        $this->assertSame(0, $result, $this->getCommandDisplayOutputErrorMessage());
        $this->assertStringContainsString("RebelMyReports_{$this->idReport}_1", $this->applicationTester->getDisplay());
        $this->assertStringContainsString("RebelMyReports_{$second}_1", $this->applicationTester->getDisplay());
    }

    public function testRearchiveBuildsTheRecordOfTheReportForTheDates()
    {
        TrackedVisits::track(1);

        $result = $this->applicationTester->run([
            'command' => 'rebelmyreports:archive',
            '--idmyreport' => (string) $this->idReport,
            '--rearchive' => true,
            '--date' => TrackedVisits::DAY,
            '--period' => 'day',
        ]);

        $archived = Db::fetchAll(
            'SELECT DISTINCT name, date1, period FROM ' . Common::prefixTable('archive_blob_2024_03') . " WHERE name LIKE 'RebelMyReports%'"
        );

        $this->assertSame(0, $result, $this->getCommandDisplayOutputErrorMessage());
        // Core also builds the week, month and year the day belongs to.
        $this->assertSame(["RebelMyReports_{$this->idReport}_1"], array_values(array_unique(array_column($archived, 'name'))));
        $this->assertContains(
            ['name' => "RebelMyReports_{$this->idReport}_1", 'date1' => TrackedVisits::DAY, 'period' => 1],
            $archived
        );
    }
}
