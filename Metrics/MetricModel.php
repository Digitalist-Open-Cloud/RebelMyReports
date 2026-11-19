<?php

namespace Piwik\Plugins\RebelMyReports\Metrics;

use Piwik\Common;
use Piwik\Date;
use Piwik\Db;
use Piwik\DbHelper;

/**
 * Stores the calculated metrics.
 */
class MetricModel
{
    public const TABLE = 'rebel_my_metrics';

    private string $table;

    public function __construct()
    {
        $this->table = Common::prefixTable(self::TABLE);
    }

    public function install(): void
    {
        DbHelper::createTable(self::TABLE, "
            `idmetric` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `idsite` INT UNSIGNED NOT NULL,
            `name` VARCHAR(100) NOT NULL,
            `formula` VARCHAR(1000) NOT NULL,
            `format` VARCHAR(20) NOT NULL,
            `created_date` DATETIME NOT NULL,
            `updated_date` DATETIME NOT NULL,
            PRIMARY KEY (`idmetric`),
            KEY `idsite` (`idsite`)");
    }

    public function uninstall(): void
    {
        Db::dropTables([$this->table]);
    }

    public function addMetric(int $idSite, string $name, string $formula, string $format): int
    {
        $now = Date::now()->getDatetime();

        Db::query(
            "INSERT INTO {$this->table} (`idsite`, `name`, `formula`, `format`, `created_date`, `updated_date`) VALUES (?, ?, ?, ?, ?, ?)",
            [$idSite, $name, $formula, $format, $now, $now]
        );

        return (int) Db::fetchOne('SELECT LAST_INSERT_ID()');
    }

    public function updateMetric(int $idMetric, string $name, string $formula, string $format): void
    {
        Db::query(
            "UPDATE {$this->table} SET `name` = ?, `formula` = ?, `format` = ?, `updated_date` = ? WHERE `idmetric` = ?",
            [$name, $formula, $format, Date::now()->getDatetime(), $idMetric]
        );
    }

    public function deleteMetric(int $idMetric): void
    {
        Db::query("DELETE FROM {$this->table} WHERE `idmetric` = ?", [$idMetric]);
    }

    public function deleteMetricsOfSite(int $idSite): void
    {
        Db::query("DELETE FROM {$this->table} WHERE `idsite` = ?", [$idSite]);
    }

    public function getMetric(int $idMetric): ?array
    {
        $row = Db::fetchRow("SELECT * FROM {$this->table} WHERE `idmetric` = ?", [$idMetric]);

        return $row ? $this->fromRow($row) : null;
    }

    /**
     * @return array[]
     */
    public function getMetrics(int $idSite): array
    {
        $rows = Db::fetchAll("SELECT * FROM {$this->table} WHERE `idsite` = ? ORDER BY `name`, `idmetric`", [$idSite]);

        return array_map([$this, 'fromRow'], $rows);
    }

    private function fromRow(array $row): array
    {
        $row['idmetric'] = (int) $row['idmetric'];
        $row['idsite'] = (int) $row['idsite'];

        return $row;
    }
}
