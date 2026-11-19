<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Common;
use Piwik\Date;
use Piwik\Db;
use Piwik\DbHelper;

class Model
{
    public const TABLE = 'rebel_my_reports';

    public const TYPE_TABLE = 'table';
    public const TYPE_EVOLUTION = 'evolution';
    public const TYPE_KEY_FIGURE = 'keyfigure';

    public const SORT_ASC = 'asc';
    public const SORT_DESC = 'desc';
    public const FILTER_OPERATORS = ['gt', 'gte', 'lt', 'lte', 'eq'];

    public const ROW_SETTING_COLUMNS = [
        'sort_metric' => "VARCHAR(100) NOT NULL DEFAULT ''",
        'sort_order' => "VARCHAR(4) NOT NULL DEFAULT 'desc'",
        'row_limit' => 'INT UNSIGNED NOT NULL DEFAULT 0',
    ];

    public const METRIC_FILTERS_COLUMN = 'TEXT NOT NULL';
    public const VALUE_GROUPS_COLUMN = 'TEXT NOT NULL';

    // The IDs of the core visualizations a table report can open with.
    public const VISUALIZATIONS = ['table', 'graphVerticalBar', 'graphPie', 'cloud'];
    public const VISUALIZATION_COLUMN = "VARCHAR(30) NOT NULL DEFAULT 'table'";

    public const STATE_COLUMNS = [
        'paused' => 'TINYINT(1) UNSIGNED NOT NULL DEFAULT 0',
        'created_by' => "VARCHAR(100) NOT NULL DEFAULT ''",
        'updated_by' => "VARCHAR(100) NOT NULL DEFAULT ''",
    ];

    public const POSITION_COLUMN = 'INT UNSIGNED NOT NULL DEFAULT 0';

    private string $table;

    public function __construct()
    {
        $this->table = Common::prefixTable(self::TABLE);
    }

    public function install(): void
    {
        $rowSettings = '';
        foreach (self::ROW_SETTING_COLUMNS as $column => $type) {
            $rowSettings .= "`$column` $type,\n";
        }
        $state = '';
        foreach (self::STATE_COLUMNS as $column => $type) {
            $state .= "`$column` $type,\n";
        }

        DbHelper::createTable(self::TABLE, "
            `idreport` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `idsite` INT UNSIGNED NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `description` VARCHAR(1000) NOT NULL DEFAULT '',
            `report_type` VARCHAR(20) NOT NULL,
            `dimensions` TEXT NOT NULL,
            `metrics` TEXT NOT NULL,
            `segment_filter` TEXT NOT NULL,
            `category` VARCHAR(100) NOT NULL DEFAULT '',
            `subcategory` VARCHAR(100) NOT NULL DEFAULT '',
            $rowSettings
            `metric_filters` " . self::METRIC_FILTERS_COLUMN . ",
            `value_groups` " . self::VALUE_GROUPS_COLUMN . ",
            `visualization` " . self::VISUALIZATION_COLUMN . ",
            `revision` INT UNSIGNED NOT NULL DEFAULT 1,
            `position` " . self::POSITION_COLUMN . ",
            $state
            `created_date` DATETIME NOT NULL,
            `updated_date` DATETIME NOT NULL,
            PRIMARY KEY (`idreport`),
            KEY `idsite` (`idsite`)");
    }

    public function uninstall(): void
    {
        Db::dropTables([$this->table]);
    }

    /**
     * @param array{idsite:int,name:string,description?:string,report_type:string,dimensions:string[],metrics:string[],segment_filter?:string,category?:string,subcategory?:string,sort_metric?:string,sort_order?:string,row_limit?:int,metric_filters?:array[],value_groups?:array[],visualization?:string} $report
     */
    public function addReport(array $report, string $login = ''): int
    {
        $now = Date::now()->getDatetime();
        $row = $this->toRow($report) + ['created_date' => $now, 'updated_date' => $now, 'created_by' => $login, 'updated_by' => $login];

        $columns = implode(', ', array_map(fn ($column) => "`$column`", array_keys($row)));
        $placeholders = implode(', ', array_fill(0, count($row), '?'));

        Db::query("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)", array_values($row));

        $idMyReport = (int) Db::fetchOne('SELECT LAST_INSERT_ID()');
        // A new report comes last: no position is higher than the highest ID.
        Db::query("UPDATE {$this->table} SET `position` = `idreport` WHERE `idreport` = ?", [$idMyReport]);

        return $idMyReport;
    }

    /**
     * @param bool $newRevision whether the change makes the data archived so far invalid. The
     *                          revision is then bumped so that it is never reused.
     */
    public function updateReport(int $idMyReport, array $report, bool $newRevision, string $login = ''): void
    {
        $columns = $this->toRow($report);
        unset($columns['idsite']);
        $columns['updated_date'] = Date::now()->getDatetime();
        $columns['updated_by'] = $login;

        $set = implode(', ', array_map(fn ($column) => "`$column` = ?", array_keys($columns)));
        if ($newRevision) {
            $set .= ', `revision` = `revision` + 1';
        }

        Db::query(
            "UPDATE {$this->table} SET $set WHERE `idreport` = ?",
            array_merge(array_values($columns), [$idMyReport])
        );
    }

    /**
     * Marks the data archived for the report so far as outdated.
     */
    public function startNewRevision(int $idMyReport): void
    {
        Db::query(
            "UPDATE {$this->table} SET `revision` = `revision` + 1, `updated_date` = ? WHERE `idreport` = ?",
            [Date::now()->getDatetime(), $idMyReport]
        );
    }

    public function setPaused(int $idMyReport, bool $paused, string $login = ''): void
    {
        Db::query(
            "UPDATE {$this->table} SET `paused` = ?, `updated_date` = ?, `updated_by` = ? WHERE `idreport` = ?",
            [(int) $paused, Date::now()->getDatetime(), $login, $idMyReport]
        );
    }

    /**
     * @param array<int, int> $positions report ID => position
     */
    public function setPositions(array $positions): void
    {
        foreach ($positions as $idMyReport => $position) {
            Db::query("UPDATE {$this->table} SET `position` = ? WHERE `idreport` = ?", [$position, $idMyReport]);
        }
    }

    public function deleteReport(int $idMyReport): void
    {
        Db::query("DELETE FROM {$this->table} WHERE `idreport` = ?", [$idMyReport]);
    }

    public function getReport(int $idMyReport): ?array
    {
        $row = Db::fetchRow("SELECT * FROM {$this->table} WHERE `idreport` = ?", [$idMyReport]);

        return $row ? $this->fromRow($row) : null;
    }

    /**
     * Returns the reports of the site, including the reports defined for all sites.
     */
    public function getReports(int $idSite): array
    {
        $rows = Db::fetchAll(
            "SELECT * FROM {$this->table} WHERE `idsite` IN (0, ?) ORDER BY `position`, `idreport`",
            [$idSite]
        );

        return array_map([$this, 'fromRow'], $rows);
    }

    /**
     * Deletes the reports defined for the website and returns them.
     *
     * @return array[]
     */
    public function deleteReportsOfSite(int $idSite): array
    {
        $rows = Db::fetchAll("SELECT * FROM {$this->table} WHERE `idsite` = ?", [$idSite]);
        Db::query("DELETE FROM {$this->table} WHERE `idsite` = ?", [$idSite]);

        return array_map([$this, 'fromRow'], $rows);
    }

    public function getAllReports(): array
    {
        $rows = Db::fetchAll("SELECT * FROM {$this->table} ORDER BY `position`, `idreport`");

        return array_map([$this, 'fromRow'], $rows);
    }

    private function toRow(array $report): array
    {
        return [
            'idsite' => (int) $report['idsite'],
            'name' => $report['name'],
            'description' => $report['description'] ?? '',
            'report_type' => $report['report_type'],
            'dimensions' => json_encode(array_values($report['dimensions'])),
            'metrics' => json_encode(array_values($report['metrics'])),
            'segment_filter' => $report['segment_filter'] ?? '',
            'category' => $report['category'] ?? '',
            'subcategory' => $report['subcategory'] ?? '',
            'sort_metric' => $report['sort_metric'] ?? '',
            'sort_order' => $report['sort_order'] ?? self::SORT_DESC,
            'row_limit' => (int) ($report['row_limit'] ?? 0),
            'metric_filters' => json_encode(array_values($report['metric_filters'] ?? [])),
            'value_groups' => json_encode(array_values($report['value_groups'] ?? [])),
            'visualization' => $report['visualization'] ?? self::VISUALIZATIONS[0],
        ];
    }

    private function fromRow(array $row): array
    {
        $row['idreport'] = (int) $row['idreport'];
        $row['idsite'] = (int) $row['idsite'];
        $row['revision'] = (int) $row['revision'];
        $row['position'] = (int) $row['position'];
        $row['paused'] = (bool) $row['paused'];
        $row['row_limit'] = (int) $row['row_limit'];
        $row['metric_filters'] = json_decode($row['metric_filters'], true);
        $row['value_groups'] = json_decode($row['value_groups'], true);
        $row['dimensions'] = json_decode($row['dimensions'], true);
        $row['metrics'] = json_decode($row['metrics'], true);

        return $row;
    }
}
