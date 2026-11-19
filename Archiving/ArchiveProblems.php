<?php

namespace Piwik\Plugins\RebelMyReports\Archiving;

use Exception;
use Piwik\Db;
use Piwik\Option;
use Piwik\Updater\Migration\Db as DbErrors;

/**
 * Remembers for each report and website the last period that could not be archived, or not
 * completely, until that period is archived without the problem.
 */
class ArchiveProblems
{
    public const TIMEOUT = 'timeout';
    public const FAILED = 'failed';
    public const TRUNCATED = 'truncated';

    private const OPTION_PREFIX = 'RebelMyReports_archiveProblem_';

    /**
     * @param string $period the date range of the period, such as "2024-03-05,2024-03-05"
     */
    public function add(string $recordName, int $idSite, string $type, string $period): void
    {
        Option::set($this->getOptionName($recordName, $idSite), json_encode(['type' => $type, 'period' => $period]));
    }

    /**
     * Forgets the problem of the period after it was archived without one.
     */
    public function resolve(string $recordName, int $idSite, string $period): void
    {
        if (($this->get($recordName, $idSite)['period'] ?? null) === $period) {
            Option::delete($this->getOptionName($recordName, $idSite));
        }
    }

    /**
     * @return array{type: string, period: string}|null
     */
    public function get(string $recordName, int $idSite): ?array
    {
        $problem = json_decode((string) Option::get($this->getOptionName($recordName, $idSite)), true);

        return is_array($problem) ? $problem : null;
    }

    /**
     * Forgets the problems of a record on all websites.
     */
    public function forget(string $recordName): void
    {
        Option::deleteLike(str_replace('_', '\_', self::OPTION_PREFIX . $recordName) . '\_%');
    }

    /**
     * Whether the database stopped a query because it ran longer than allowed.
     */
    public function isTimeout(Exception $e): bool
    {
        $db = Db::get();

        return $db->isErrNo($e, DbErrors::ERROR_CODE_MAX_EXECUTION_TIME_EXCEEDED_QUERY_INTERRUPTED)
            || $db->isErrNo($e, DbErrors::ERROR_CODE_MAX_STATEMENT_TIME_EXCEEDED_QUERY_INTERRUPTED);
    }

    private function getOptionName(string $recordName, int $idSite): string
    {
        return self::OPTION_PREFIX . $recordName . '_' . $idSite;
    }
}
