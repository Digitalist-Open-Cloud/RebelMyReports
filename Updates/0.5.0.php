<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Common;
use Piwik\Updater;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Piwik\Updates as PiwikUpdates;

/**
 * Adds the position of a report in the order of the reports.
 */
class Updates_0_5_0 extends PiwikUpdates
{
    private MigrationFactory $migration;

    public function __construct(MigrationFactory $factory)
    {
        $this->migration = $factory;
    }

    public function getMigrations(Updater $updater)
    {
        return [
            $this->migration->db->addColumn(Model::TABLE, 'position', Model::POSITION_COLUMN, 'revision'),
            $this->migration->db->sql('UPDATE `' . Common::prefixTable(Model::TABLE) . '` SET `position` = `idreport`'),
        ];
    }

    public function doUpdate(Updater $updater)
    {
        $updater->executeMigrations(__FILE__, $this->getMigrations($updater));
    }
}
