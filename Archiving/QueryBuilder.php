<?php

namespace Piwik\Plugins\RebelMyReports\Archiving;

use Exception;
use Piwik\Columns\Dimension;
use Piwik\Container\StaticContainer;
use Piwik\DataAccess\LogAggregator;
use Piwik\DataAccess\LogQueryBuilder;
use Piwik\Piwik;
use Piwik\Plugin\ArchivedMetric;
use Piwik\Plugin\ProcessedMetric;
use Piwik\Plugin\LogTablesProvider;
use Piwik\Plugins\RebelMyReports\Catalogue;
use Piwik\Tracker\Action;

/**
 * Builds the log queries of a report from the SQL definitions of Matomo's dimensions and
 * metrics. Only catalogue IDs are accepted, so no request input reaches the SQL.
 */
class QueryBuilder
{
    public const OP_SUM = 'sum';
    public const OP_MAX = 'max';
    public const OP_MIN = 'min';
    public const OP_UNIQUE = 'uniq';

    // ponytail: hard cap per query, ordered by the first metric. Metrics from different log
    // tables may keep different rows beyond it. Use a ranking query if reports hit the cap.
    public const MAX_ROWS_PER_QUERY = 50000;

    private const DERIVED_TABLE = 'rmr';
    private const TABLE_COLUMN_REGEX = '/\b(log_[a-z_]+)\.([a-z_0-9]+)\b/';

    private Catalogue $catalogue;
    private LogTablesProvider $logTablesProvider;

    public function __construct(Catalogue $catalogue, LogTablesProvider $logTablesProvider)
    {
        $this->catalogue = $catalogue;
        $this->logTablesProvider = $logTablesProvider;
    }

    /**
     * Returns the metrics to archive for the selected ones: computed and calculated metrics
     * are replaced by the archived metrics they are calculated from.
     *
     * @param string[] $metricNames
     * @return ArchivedMetric[] indexed by metric name
     */
    public function getArchivedMetrics(array $metricNames): array
    {
        $archived = [];
        foreach ($metricNames as $name) {
            $this->collectArchivedMetrics($name, $archived);
        }

        return $archived;
    }

    public function getAggregationOp(ArchivedMetric $metric): string
    {
        $query = strtolower(ltrim($metric->getQuery()));

        if (preg_match('/^count\(distinct ([^()]+)\)$/', $query, $matches)) {
            return $this->countsRows($metric, $matches[1]) ? self::OP_SUM : self::OP_UNIQUE;
        }
        if (strpos($query, 'max(') === 0) {
            return self::OP_MAX;
        }
        if (strpos($query, 'min(') === 0) {
            return self::OP_MIN;
        }

        return self::OP_SUM;
    }

    /**
     * Whether a distinct count is over the rows of the metric's table (visits, actions,
     * conversions). Such counts can be added up over days, unlike counts of distinct values.
     */
    private function countsRows(ArchivedMetric $metric, string $distinctColumns): bool
    {
        $table = $metric->getDbTableName();
        $primaryKey = $this->logTablesProvider->getLogTable($table)->getPrimaryKey();

        // A column fixed by the metric's filter (the goal of a goal metric) identifies nothing.
        $discriminator = $metric->getDimension()->getDbDiscriminator();
        if ($discriminator && $discriminator->getTable() === $table) {
            $primaryKey = array_diff($primaryKey, [$discriminator->getColumn()]);
        }

        $columns = array_map('trim', explode(',', str_replace($table . '.', '', $distinctColumns)));
        sort($columns);
        sort($primaryKey);

        return !empty($primaryKey) && $columns === array_values($primaryKey);
    }

    /**
     * Returns one query per log table (and action type filter) the metrics read from. Each
     * selects the dimensions as `d0`, `d1`, ... and the metrics under their names.
     *
     * @param array $dimensionIds dimension IDs. Anything that is not a known ID is rejected.
     * @param ArchivedMetric[] $metrics indexed by metric name
     * @param array[] $valueGroups the validated value groups of the report
     * @return array<int, array{sql: string, bind: array}>
     * @throws Exception if the selection cannot be queried
     */
    public function buildQueries(LogAggregator $logAggregator, array $dimensionIds, array $metrics, array $valueGroups = []): array
    {
        $valueGroups = new ValueGroups($valueGroups);
        $available = $this->catalogue->getDimensions();
        $dimensions = [];
        foreach ($dimensionIds as $id) {
            if (!is_string($id) || !isset($available[$id])) {
                throw new Exception(Piwik::translate('RebelMyReports_ErrorUnknownDimension', is_string($id) ? $id : gettype($id)));
            }
            $dimensions[] = $available[$id];
        }

        $groups = [];
        foreach ($metrics as $name => $metric) {
            $table = $metric->getDbTableName();
            $filter = $this->getDiscriminatorSql($metric->getDimension(), $table, 'rmr_mf');
            $key = $table . '|' . json_encode($filter);

            $groups[$key]['table'] = $table;
            $groups[$key]['filter'] = $filter;
            $groups[$key]['metrics'][$name] = $metric;
        }

        $queries = [];
        foreach ($groups as $group) {
            $queries[] = $this->buildQuery($logAggregator, $dimensions, $group['table'], $group['filter'], $group['metrics'], $valueGroups);
        }

        return $queries;
    }

    /**
     * @param ArchivedMetric[] $archived
     */
    private function collectArchivedMetrics(string $name, array &$archived): void
    {
        $metric = $this->catalogue->findMetric($name);

        if ($metric instanceof ArchivedMetric && $this->catalogue->getMetricTables($metric) !== null) {
            $archived[$name] = $metric;
            return;
        }

        if ($metric instanceof ProcessedMetric && $this->catalogue->getMetricTables($metric) !== null) {
            foreach ($metric->getDependentMetrics() as $dependent) {
                $this->collectArchivedMetrics($dependent, $archived);
            }
            return;
        }

        throw new Exception(Piwik::translate('RebelMyReports_ErrorUnknownMetric', $name));
    }

    /**
     * @param Dimension[] $dimensions
     * @param array{from: array, where: string[]} $filter
     * @param ArchivedMetric[] $metrics
     * @return array{sql: string, bind: array}
     */
    private function buildQuery(
        LogAggregator $logAggregator,
        array $dimensions,
        string $table,
        array $filter,
        array $metrics,
        ValueGroups $valueGroups
    ): array {
        $from = [$table];
        $aliasJoins = $filter['from'];
        $where = $filter['where'];
        $dimensionSelects = [];
        $dimensionAliases = [];

        foreach ($dimensions as $index => $dimension) {
            $dimensionTable = $dimension->getDbTableName();
            $from[] = $dimensionTable;

            $sql = $this->getDimensionSql($dimension, 'rmr_d' . $index);
            $aliasJoins = array_merge($aliasJoins, $sql['from']);
            $where = array_merge($where, $sql['where']);

            $dimensionAliases[] = 'd' . $index;
            $dimensionSelects[] = $valueGroups->toSql($dimension->getId(), $sql['select']) . ' AS d' . $index;
        }

        foreach ($metrics as $metric) {
            preg_match_all(self::TABLE_COLUMN_REGEX, $metric->getQuery(), $matches);
            $from = array_merge($from, $matches[1]);
        }

        $logTable = $this->logTablesProvider->getLogTable($table);
        $dateTable = $table;
        $dateColumn = $logTable->getDateTimeColumn();
        if (!$dateColumn) {
            $dateTable = 'log_visit';
            $dateColumn = $this->logTablesProvider->getLogTable('log_visit')->getDateTimeColumn();
            $from[] = 'log_visit';
        }

        $from = array_values(array_unique($from));
        $whereSql = $logAggregator->getWhereStatement($dateTable, $dateColumn);
        if ($where) {
            $whereSql .= ' AND ' . implode(' AND ', $where);
        }

        $groupBy = implode(', ', $dimensionAliases);
        $orderBy = '`' . array_key_first($metrics) . '` DESC';

        // Joining anything but the visit repeats the rows of the metric's table.
        $repeatsRows = (bool) array_diff($from, [$table, 'log_visit']);
        $from = array_merge($from, $aliasJoins);

        if (!$repeatsRows) {
            $select = $dimensionSelects;
            foreach ($metrics as $name => $metric) {
                $select[] = $metric->getQuery() . " AS `$name`";
            }

            return $this->generateQuery($logAggregator, implode(', ', $select), $from, $whereSql, $groupBy, $orderBy, self::MAX_ROWS_PER_QUERY);
        }

        return $this->buildDeduplicatedQuery($logAggregator, $table, $metrics, $dimensionSelects, $from, $whereSql, $groupBy, $orderBy);
    }

    /**
     * Selects each row of the metric's table once per dimension value, then aggregates.
     *
     * @param ArchivedMetric[] $metrics
     * @param string[] $dimensionSelects
     * @return array{sql: string, bind: array}
     */
    private function buildDeduplicatedQuery(
        LogAggregator $logAggregator,
        string $table,
        array $metrics,
        array $dimensionSelects,
        array $from,
        string $where,
        string $groupBy,
        string $orderBy
    ): array {
        $primaryKey = $this->logTablesProvider->getLogTable($table)->getPrimaryKey();
        if (empty($primaryKey)) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorNotCombinable', $table));
        }

        $columns = [];
        foreach ($primaryKey as $column) {
            $columns["$table.$column"] = "{$table}__$column";
        }

        $outerSelect = $groupBy ? [$groupBy] : [];
        foreach ($metrics as $name => $metric) {
            preg_match_all(self::TABLE_COLUMN_REGEX, $metric->getQuery(), $matches, PREG_SET_ORDER);
            foreach ($matches as $match) {
                $columns[$match[0]] = $match[1] . '__' . $match[2];
            }

            $outerSelect[] = preg_replace(self::TABLE_COLUMN_REGEX, self::DERIVED_TABLE . '.$1__$2', $metric->getQuery()) . " AS `$name`";
        }

        $innerSelect = $dimensionSelects;
        foreach ($columns as $column => $alias) {
            $innerSelect[] = "$column AS $alias";
        }

        $inner = $this->generateQuery($logAggregator, 'DISTINCT ' . implode(', ', $innerSelect), $from, $where, '', '');

        $sql = 'SELECT ' . implode(', ', $outerSelect)
            . ' FROM (' . $inner['sql'] . ') AS ' . self::DERIVED_TABLE
            . ($groupBy ? ' GROUP BY ' . $groupBy : '')
            . ' ORDER BY ' . $orderBy
            . ' LIMIT ' . self::MAX_ROWS_PER_QUERY;

        return ['sql' => $sql, 'bind' => $inner['bind']];
    }

    /**
     * @return array{sql: string, bind: array}
     */
    private function generateQuery(LogAggregator $logAggregator, string $select, array $from, string $where, string $groupBy, string $orderBy, int $limit = 0): array
    {
        // Core otherwise wraps joins on log_visit in a subquery that keeps one row per visit.
        $logQueryBuilder = StaticContainer::get(LogQueryBuilder::class);
        $previous = $logQueryBuilder->getForcedInnerGroupBySubselect();
        $logQueryBuilder->forceInnerGroupBySubselect(LogQueryBuilder::FORCE_INNER_GROUP_BY_NO_SUBSELECT);

        try {
            return $logAggregator->generateQuery($select, $from, $where, $groupBy, $orderBy, $limit);
        } finally {
            $logQueryBuilder->forceInnerGroupBySubselect($previous);
        }
    }

    /**
     * @return array{select: string, from: array, where: string[]}
     */
    private function getDimensionSql(Dimension $dimension, string $alias): array
    {
        $table = $dimension->getDbTableName();
        $select = $dimension->getSqlSegment();
        $sql = $this->getDiscriminatorSql($dimension, $table, $alias . 't');

        $join = $dimension->getDbColumnJoin();
        if ($join) {
            $on = "$table.{$dimension->getColumnName()} = $alias.{$join->getColumn()}";
            foreach ($join->getAdditionalKeyColumns() as $keyColumn) {
                $on .= " AND $table.$keyColumn = $alias.$keyColumn";
            }

            $sql['from'][] = ['table' => $join->getTable(), 'tableAlias' => $alias, 'joinOn' => $on];
            $select = "$alias.{$join->getTargetColumn()}";
        }

        if ($dimension->getType() === Dimension::TYPE_BINARY) {
            $select = "LOWER(HEX($select))";
        }

        return ['select' => $select] + $sql;
    }

    /**
     * Returns the joins and conditions that restrict rows to the kind the dimension is about,
     * for example page views for "Page URL".
     *
     * @return array{from: array, where: string[]}
     */
    private function getDiscriminatorSql(Dimension $dimension, string $table, string $alias): array
    {
        $discriminator = $dimension->getDbDiscriminator();
        if (!$discriminator) {
            return ['from' => [], 'where' => []];
        }

        $value = (int) $discriminator->getValue();
        $column = $discriminator->getColumn();

        if ($discriminator->getTable() === $table) {
            return ['from' => [], 'where' => ["$table.$column = $value"]];
        }

        if ($discriminator->getTable() !== 'log_action') {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorNotCombinable', $discriminator->getTable()));
        }

        $actionColumn = $dimension->getDbColumnJoin() ? $dimension->getColumnName() : 'idaction_url';
        if ($table === 'log_link_visit_action' && $value === Action::TYPE_EVENT) {
            // An action is an event when it has an event category, whatever column is reported.
            $actionColumn = 'idaction_event_category';
        }

        $where = [];
        $eventTypes = [Action::TYPE_EVENT_CATEGORY, Action::TYPE_EVENT_ACTION, Action::TYPE_EVENT_NAME];
        if ($table === 'log_link_visit_action' && !in_array($value, $eventTypes, true)) {
            // Events carry the URL and title of their page, but are not page actions.
            $where[] = "$table.idaction_event_category IS NULL";
        }

        return [
            'from' => [[
                'table' => 'log_action',
                'tableAlias' => $alias,
                'join' => 'INNER JOIN',
                'joinOn' => "$table.$actionColumn = $alias.idaction AND $alias.$column = $value",
            ]],
            'where' => $where,
        ];
    }
}
