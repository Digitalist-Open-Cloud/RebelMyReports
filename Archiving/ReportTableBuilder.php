<?php

namespace Piwik\Plugins\RebelMyReports\Archiving;

use Exception;
use Piwik\DataAccess\LogAggregator;
use Piwik\DataTable;
use Piwik\DbHelper;
use Piwik\Plugin\ArchivedMetric;

/**
 * Queries the logs for a report definition and returns the result as a DataTable. Dimensions
 * after the first become subtables.
 */
class ReportTableBuilder
{
    public const LABEL_TOTAL = 'total';
    public const TRUNCATED_METADATA = 'rebelMyReportsTruncated';

    private QueryBuilder $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param string[] $dimensionIds
     * @param string[] $metricNames
     * @param int $maxExecutionTime how many seconds a query may run, 0 for no limit of its own
     * @param array[] $valueGroups the validated value groups of the report
     * @throws Exception if the selection cannot be queried or a query runs too long
     */
    public function build(
        LogAggregator $logAggregator,
        array $dimensionIds,
        array $metricNames,
        int $maxExecutionTime = 0,
        array $valueGroups = []
    ): DataTable {
        $metrics = $this->queryBuilder->getArchivedMetrics($metricNames);
        $ops = $this->getAggregationOps($metrics);

        $table = new DataTable();
        $table->setMetadata(DataTable::COLUMN_AGGREGATION_OPS_METADATA_NAME, $ops);

        // Each level has its own queries: a parent row is not the sum of its subtable for
        // unique, max and min metrics.
        for ($depth = min(1, count($dimensionIds)); $depth <= count($dimensionIds); $depth++) {
            $queries = $this->queryBuilder->buildQueries($logAggregator, array_slice($dimensionIds, 0, $depth), $metrics, $valueGroups);

            foreach ($queries as $query) {
                $sql = DbHelper::addMaxExecutionTimeHintToQuery($query['sql'], $maxExecutionTime);
                $cursor = $logAggregator->getDb()->query($sql, $query['bind']);
                $rows = 0;
                while ($row = $cursor->fetch()) {
                    $this->addRow($table, $row, $depth, $ops);
                    $rows++;
                }
                $cursor->closeCursor();

                if ($rows >= QueryBuilder::MAX_ROWS_PER_QUERY) {
                    // The query was cut off, so the rows with the lowest values are missing.
                    $table->setMetadata(self::TRUNCATED_METADATA, true);
                }
            }
        }

        return $table;
    }

    /**
     * @param ArchivedMetric[] $metrics indexed by metric name
     * @return array<string, string> metric name => aggregation operation, for metrics that are not summed
     */
    public function getAggregationOps(array $metrics): array
    {
        $ops = [];
        foreach ($metrics as $name => $metric) {
            $op = $this->queryBuilder->getAggregationOp($metric);
            if ($op === QueryBuilder::OP_MAX || $op === QueryBuilder::OP_MIN) {
                $ops[$name] = $op;
            }
        }

        return $ops;
    }

    private function addRow(DataTable $table, array $row, int $depth, array $ops): void
    {
        $labels = [];
        $columns = [];
        foreach ($row as $name => $value) {
            if (preg_match('/^d\d$/', $name)) {
                $labels[] = (string) $value;
            } else {
                $columns[$name] = $value + 0;
            }
        }

        if ($depth === 0) {
            $table->sumRowWithLabel(self::LABEL_TOTAL, $columns, $ops);
            return;
        }

        $tableRow = $table->sumRowWithLabel($labels[0], $depth === 1 ? $columns : [], $ops);
        for ($level = 1; $level < $depth; $level++) {
            $tableRow = $tableRow->sumRowWithLabelToSubtable($labels[$level], $level === $depth - 1 ? $columns : [], $ops);
        }
    }
}
