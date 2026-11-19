<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Common;
use Piwik\Updater;
use Piwik\Updater\Migration\Factory as MigrationFactory;
use Piwik\Updates as PiwikUpdates;

/**
 * Lets a report have several metric filters: moves the single one into a list.
 */
class Updates_0_6_0 extends PiwikUpdates
{
    private MigrationFactory $migration;

    public function __construct(MigrationFactory $factory)
    {
        $this->migration = $factory;
    }

    public function getMigrations(Updater $updater)
    {
        $filter = <<<'SQL'
            CONCAT('[{"metric":"', `filter_metric`, '","operator":"', `filter_operator`, '","value":', `filter_value`, '}]')
            SQL;

        return [
            $this->migration->db->addColumn(Model::TABLE, 'metric_filters', Model::METRIC_FILTERS_COLUMN, 'row_limit'),
            $this->migration->db->sql(
                'UPDATE `' . Common::prefixTable(Model::TABLE) . "` SET `metric_filters` = IF(`filter_metric` = '', '[]', $filter)"
            ),
            $this->migration->db->dropColumns(Model::TABLE, ['filter_metric', 'filter_operator', 'filter_value']),
        ];
    }

    public function doUpdate(Updater $updater)
    {
        $updater->executeMigrations(__FILE__, $this->getMigrations($updater));
    }
}
