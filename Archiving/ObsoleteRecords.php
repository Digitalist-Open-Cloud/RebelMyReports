<?php

namespace Piwik\Plugins\RebelMyReports\Archiving;

use Piwik\DataAccess\ArchiveTableCreator;
use Piwik\Db;
use Piwik\Option;

/**
 * Archive records of reports that were deleted or whose data changed. They are remembered
 * and removed by a scheduled task, as deleting from every archive table can take long.
 */
class ObsoleteRecords
{
    private const OPTION_NAME = 'RebelMyReports_obsoleteRecords';

    /**
     * @param int $idSite the website the record was archived for, 0 for all websites
     */
    public function add(string $recordName, int $idSite): void
    {
        $records = $this->getAll();
        $records[] = ['record' => $recordName, 'idsite' => $idSite];

        Option::set(self::OPTION_NAME, json_encode($records));
    }

    /**
     * @return array<int, array{record: string, idsite: int}>
     */
    public function getAll(): array
    {
        $records = json_decode((string) Option::get(self::OPTION_NAME), true);

        return is_array($records) ? $records : [];
    }

    /**
     * Deletes the remembered records from all archive tables.
     */
    public function purge(): void
    {
        $records = $this->getAll();
        if (empty($records)) {
            return;
        }

        foreach (ArchiveTableCreator::getTablesArchivesInstalled(ArchiveTableCreator::BLOB_TABLE) as $table) {
            foreach ($records as $record) {
                // Large tables are stored in chunks named after the record.
                $chunks = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $record['record'] . '_chunk_') . '%';
                $where = '(`name` = ? OR `name` LIKE ?)';
                $bind = [$record['record'], $chunks];

                if ($record['idsite'] > 0) {
                    $where .= ' AND `idsite` = ?';
                    $bind[] = $record['idsite'];
                }

                Db::query("DELETE FROM `$table` WHERE $where", $bind);
            }
        }

        Option::delete(self::OPTION_NAME);
    }
}
