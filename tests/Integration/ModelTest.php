<?php

namespace Piwik\Plugins\RebelMyReports\tests\Integration;

use Piwik\Plugins\RebelMyReports\Model;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group RebelMyReports
 * @group Plugins
 */
class ModelTest extends IntegrationTestCase
{
    private Model $model;

    public function setUp(): void
    {
        parent::setUp();

        $this->model = new Model();
        $this->model->install();
    }

    public function testStoresAndReturnsAReport()
    {
        $idMyReport = $this->model->addReport($this->report());

        $report = $this->model->getReport($idMyReport);

        $this->assertSame('Pages by country', $report['name']);
        $this->assertSame(['UserCountry.Country', 'Actions.PageUrl'], $report['dimensions']);
        $this->assertSame(['nb_visits', 'hits'], $report['metrics']);
        $this->assertSame(1, $report['revision']);
    }

    public function testUpdateChangesDefinitionAndBumpsRevisionWhenAsked()
    {
        $idMyReport = $this->model->addReport($this->report());

        $this->model->updateReport($idMyReport, ['name' => 'Renamed'] + $this->report(), false);
        $this->assertSame(1, $this->model->getReport($idMyReport)['revision']);

        $this->model->updateReport($idMyReport, ['name' => 'Renamed', 'metrics' => ['nb_visits']] + $this->report(), true);

        $report = $this->model->getReport($idMyReport);
        $this->assertSame('Renamed', $report['name']);
        $this->assertSame(['nb_visits'], $report['metrics']);
        $this->assertSame(2, $report['revision']);
    }

    public function testGetReportsReturnsSiteAndAllSitesReports()
    {
        $this->model->addReport(['idsite' => 1, 'name' => 'Site 1'] + $this->report());
        $this->model->addReport(['idsite' => 2, 'name' => 'Site 2'] + $this->report());
        $this->model->addReport(['idsite' => 0, 'name' => 'All sites'] + $this->report());

        $names = array_column($this->model->getReports(1), 'name');

        $this->assertSame(['Site 1', 'All sites'], $names);
    }

    public function testDeleteRemovesTheReport()
    {
        $idMyReport = $this->model->addReport($this->report());

        $this->model->deleteReport($idMyReport);

        $this->assertNull($this->model->getReport($idMyReport));
    }

    private function report(): array
    {
        return [
            'idsite' => 1,
            'name' => 'Pages by country',
            'report_type' => Model::TYPE_TABLE,
            'dimensions' => ['UserCountry.Country', 'Actions.PageUrl'],
            'metrics' => ['nb_visits', 'hits'],
        ];
    }
}
