<?php

namespace Piwik\Plugins\RebelMyReports\Archiving;

use Piwik\Common;
use Piwik\CronArchive\ReArchiveList;
use Piwik\DataAccess\ArchiveTableCreator;
use Piwik\Db;
use Piwik\Plugins\RebelMyReports\Archiver;

/**
 * Tells for which days a report has archived data and whether more is waiting to be archived.
 */
class ArchiveStatus
{
    /**
     * @param array[] $reports
     * @return array<int, array{idreport: int, first_date: ?string, last_date: ?string, is_queued: bool, problem: ?array}>
     *         by report ID
     */
    public function get(array $reports, int $idSite): array
    {
        $status = [];
        $idByRecord = [];
        foreach ($reports as $report) {
            $id = $report['idreport'];
            $idByRecord[Archiver::getRecordName($report)] = $id;
            $status[$id] = [
                'idreport' => $id,
                'first_date' => null,
                'last_date' => null,
                'is_queued' => false,
                'problem' => (new ArchiveProblems())->get(Archiver::getRecordName($report), $idSite),
            ];
        }

        if (empty($status)) {
            return [];
        }

        $records = array_keys($idByRecord);
        $in = Common::getSqlStringFieldsArray($records);

        $dates = [];
        $numericTables = ArchiveTableCreator::getTablesArchivesInstalled(ArchiveTableCreator::NUMERIC_TABLE);
        foreach (ArchiveTableCreator::getTablesArchivesInstalled(ArchiveTableCreator::BLOB_TABLE) as $blobTable) {
            $numericTable = str_replace('archive_blob_', 'archive_numeric_', $blobTable);
            if (!in_array($numericTable, $numericTables, true)) {
                continue;
            }

            // The blob tables have no index on the site. The day archives of the site are found
            // in the numeric table, which has one.
            $rows = Db::fetchAll(
                "SELECT b.name, MIN(b.date1) AS first_date, MAX(b.date1) AS last_date
                   FROM `$numericTable` n
                   INNER JOIN `$blobTable` b ON b.idarchive = n.idarchive
                  WHERE n.idsite = ? AND n.period = 1 AND n.name IN ('done', 'done.RebelMyReports')
                    AND b.name IN ($in)
                  GROUP BY b.name",
                array_merge([$idSite], $records)
            );

            foreach ($rows as $row) {
                $dates[$idByRecord[$row['name']]][] = $row['first_date'];
                $dates[$idByRecord[$row['name']]][] = $row['last_date'];
            }
        }

        foreach ($dates as $id => $datesOfReport) {
            $status[$id]['first_date'] = min($datesOfReport);
            $status[$id]['last_date'] = max($datesOfReport);
        }

        foreach ($this->getQueuedRecords($records, $in, $idSite) as $record) {
            $status[$idByRecord[$record]]['is_queued'] = true;
        }

        return $status;
    }

    /**
     * @param string[] $records
     * @return string[] the records that are waiting to be archived for the site
     */
    private function getQueuedRecords(array $records, string $in, int $idSite): array
    {
        $queued = Db::fetchAll(
            'SELECT DISTINCT report FROM `' . Common::prefixTable('archive_invalidations') . "`
              WHERE idsite = ? AND report IN ($in)",
            array_merge([$idSite], $records)
        );
        $queued = array_column($queued, 'report');

        // Re-archiving that was scheduled, but not turned into invalidations by core:archive yet.
        foreach ((new ReArchiveList())->getAll() as $item) {
            $entry = json_decode($item, true);
            $idSites = $entry['idSites'] ?? [];

            if (in_array($entry['report'] ?? '', $records, true) && ($idSites === 'all' || in_array($idSite, (array) $idSites))) {
                $queued[] = $entry['report'];
            }
        }

        return array_unique($queued);
    }
}
