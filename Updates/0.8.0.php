<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Updater;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Piwik\Updates as PiwikUpdates;

/**
 * Adds the visualization a table report opens with.
 */
class Updates_0_8_0 extends PiwikUpdates
{
    private MigrationFactory $migration;

    public function __construct(MigrationFactory $factory)
    {
        $this->migration = $factory;
    }

    public function getMigrations(Updater $updater)
    {
        return [$this->migration->db->addColumn(Model::TABLE, 'visualization', Model::VISUALIZATION_COLUMN, 'value_groups')];
    }

    public function doUpdate(Updater $updater)
    {
        $updater->executeMigrations(__FILE__, $this->getMigrations($updater));
    }
}
