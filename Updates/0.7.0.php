<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Common;
use Piwik\Updater;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Piwik\Updates as PiwikUpdates;

/**
 * Adds the value groups of a report.
 */
class Updates_0_7_0 extends PiwikUpdates
{
    private MigrationFactory $migration;

    public function __construct(MigrationFactory $factory)
    {
        $this->migration = $factory;
    }

    public function getMigrations(Updater $updater)
    {
        return [
            $this->migration->db->addColumn(Model::TABLE, 'value_groups', Model::VALUE_GROUPS_COLUMN, 'metric_filters'),
            $this->migration->db->sql('UPDATE `' . Common::prefixTable(Model::TABLE) . "` SET `value_groups` = '[]'"),
        ];
    }

    public function doUpdate(Updater $updater)
    {
        $updater->executeMigrations(__FILE__, $this->getMigrations($updater));
    }
}
