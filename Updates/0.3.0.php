<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Updater;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Piwik\Updates as PiwikUpdates;

/**
 * Adds the row settings of a report: sorting, row limit and metric filter.
 */
class Updates_0_3_0 extends PiwikUpdates
{
    private MigrationFactory $migration;

    public function __construct(MigrationFactory $factory)
    {
        $this->migration = $factory;
    }

    public function getMigrations(Updater $updater)
    {
        return [$this->migration->db->addColumns(Model::TABLE, Model::ROW_SETTING_COLUMNS + [
            'filter_metric' => "VARCHAR(100) NOT NULL DEFAULT ''",
            'filter_operator' => "VARCHAR(3) NOT NULL DEFAULT ''",
            'filter_value' => 'DOUBLE NOT NULL DEFAULT 0',
        ], 'subcategory')];
    }

    public function doUpdate(Updater $updater)
    {
        $updater->executeMigrations(__FILE__, $this->getMigrations($updater));
    }
}
