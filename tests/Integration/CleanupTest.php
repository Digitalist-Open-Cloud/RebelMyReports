<?php

namespace Piwik\Plugins\RebelMyReports\tests\Integration;

use Piwik\Archive;
use Piwik\Cache;
use Piwik\Common;
use Piwik\Db;
use Piwik\Plugins\Goals\API as GoalsApi;
use Piwik\Plugins\RebelMyReports\API;
use Piwik\Plugins\RebelMyReports\Archiver;
use Piwik\Plugins\RebelMyReports\Archiving\ObsoleteRecords;
use Piwik\Plugins\RebelMyReports\Tasks;
use Piwik\Plugins\RebelMyReports\tests\Fixtures\TrackedVisits;
use Piwik\Plugins\SitesManager\API as SitesManagerApi;
use Piwik\Tests\Framework\Fixture;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group RebelMyReports
 * @group Plugins
 */
class CleanupTest extends IntegrationTestCase
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

        Fixture::createWebsite('2024-01-01 00:00:00', $ecommerce = 1);
        Fixture::createWebsite('2024-01-01 00:00:00');
        GoalsApi::getInstance()->addGoal(1, 'Signup', 'url', '/signup', 'contains');
        TrackedVisits::track(1);
        $_GET['idSite'] = 1;

        $this->api = API::getInstance();
    }

    public function tearDown(): void
    {
        unset($_GET['idSite']);
        parent::tearDown();
    }

    public function testDeletingAReportRemovesItsArchivedDataButNoOtherReports()
    {
        $deleted = $this->addReport('Deleted');
        $kept = $this->addReport('Kept');
        $this->archive();
        $this->assertSame([$deleted, $kept], $this->getArchivedRecords());

        $this->api->deleteReport(1, $this->idOf($deleted));
        $this->assertSame([$deleted, $kept], $this->getArchivedRecords(), 'data is removed by the scheduled task');

        (new Tasks())->purgeObsoleteRecords();

        $this->assertSame([$kept], $this->getArchivedRecords());
        $this->assertSame([], (new ObsoleteRecords())->getAll());
    }

    public function testChangingTheDataOfAReportRemovesWhatWasArchivedForTheOldDefinition()
    {
        $record = $this->addReport('Resolutions');
        $this->archive();
        $idMyReport = $this->idOf($record);

        $this->api->updateReport(1, $idMyReport, 'Resolutions', 'table', ['hits'], ['Resolution.Resolution']);
        (new Tasks())->purgeObsoleteRecords();

        $this->assertSame([], $this->getArchivedRecords());
        $this->assertSame(2, $this->api->getReport(1, $idMyReport)['revision']);
    }

    public function testRenamingAReportKeepsItsArchivedData()
    {
        $record = $this->addReport('Resolutions');
        $this->archive();

        $this->api->updateReport(1, $this->idOf($record), 'Renamed', 'table', ['nb_visits'], ['Resolution.Resolution']);
        (new Tasks())->purgeObsoleteRecords();

        $this->assertSame([$record], $this->getArchivedRecords());
    }

    public function testDeletingAWebsiteDeletesItsReportsButNotThoseForAllWebsites()
    {
        $this->addReport('Of website 1');
        $this->archive();
        $forAll = $this->api->addReport(1, 'For all', 'evolution', ['nb_visits'], [], '', '', '', '', true);

        SitesManagerApi::getInstance()->deleteSite(1);

        $this->assertSame(['For all'], array_column($this->api->getReports(2), 'name'));
        $this->assertSame($forAll, $this->api->getReports(2)[0]['idreport']);
        $this->assertCount(1, (new ObsoleteRecords())->getAll());
    }

    /**
     * @return string the record name
     */
    private function addReport(string $name): string
    {
        $idMyReport = $this->api->addReport(1, $name, 'table', ['nb_visits'], ['Resolution.Resolution']);

        return Archiver::getRecordName($this->api->getReport(1, $idMyReport));
    }

    private function archive(): void
    {
        Cache::getTransientCache()->flushAll();
        Archive::build(1, 'day', TrackedVisits::DAY)->getNumeric('nb_visits');
    }

    private function idOf(string $record): int
    {
        return (int) explode('_', $record)[1];
    }

    /**
     * @return string[]
     */
    private function getArchivedRecords(): array
    {
        $rows = Db::fetchAll(
            'SELECT DISTINCT name FROM ' . Common::prefixTable('archive_blob_2024_03') . " WHERE name LIKE 'RebelMyReports%' ORDER BY name"
        );

        return array_column($rows, 'name');
    }
}
