<?php

namespace Piwik\Plugins\RebelMyReports\DataTable\Filter;

use Piwik\DataTable;
use Piwik\DataTable\BaseFilter;
use Piwik\DataTable\Row;
use Piwik\Plugin\ProcessedMetric;
use Piwik\Plugins\RebelMyReports\Catalogue;
use Piwik\Plugins\RebelMyReports\Model;

/**
 * Applies the row settings of a report to each level of its table: removes the rows that do
 * not pass the metric filters, sorts the rest and keeps the first ones up to the row limit.
 */
class ApplyRowRules extends BaseFilter
{
    private array $report;
    private Catalogue $catalogue;

    /** @var string[] */
    private array $archivedMetrics;

    public function __construct(DataTable $table, array $report, Catalogue $catalogue)
    {
        parent::__construct($table);

        $this->report = $report;
        $this->catalogue = $catalogue;
        $this->archivedMetrics = array_filter(
            $report['metrics'],
            fn (string $name) => !$catalogue->findMetric($name) instanceof ProcessedMetric
        );
    }

    /**
     * @param DataTable $table
     */
    public function filter($table)
    {
        $report = $this->report;
        $rows = $table->getRowsWithoutSummaryRow();

        // A row has no value for a metric of another log table that has no logs for it. Core
        // sorts rows without a value last in both directions.
        foreach ($rows as $row) {
            foreach ($this->archivedMetrics as $name) {
                if ($row->getColumn($name) === false) {
                    $row->setColumn($name, 0);
                }
            }
        }

        foreach ($report['metric_filters'] as $filter) {
            $rows = array_filter($rows, fn (Row $row) => $this->passesFilter($this->getValue($row, $filter['metric']), $filter));
        }

        $sortMetric = $report['sort_metric'] !== '' ? $report['sort_metric'] : $report['metrics'][0];
        $direction = $report['sort_order'] === Model::SORT_ASC ? 1 : -1;
        $values = array_map(fn (Row $row) => $this->getValue($row, $sortMetric), $rows);
        uasort($values, fn ($a, $b) => $direction * ($a <=> $b));
        $rows = array_values(array_replace($values, $rows));

        if ($report['row_limit'] > 0) {
            $rows = array_slice($rows, 0, $report['row_limit']);
        }

        $table->setRows($rows);

        foreach ($rows as $row) {
            $subtable = $row->getSubtable();
            if ($subtable) {
                $this->filter($subtable);
            }
        }
    }

    /**
     * Computed and calculated metrics are not in the archived row, so they are computed here.
     */
    private function getValue(Row $row, string $metricName): float
    {
        $metric = $this->catalogue->findMetric($metricName);

        return (float) ($metric instanceof ProcessedMetric ? $metric->compute($row) : $row->getColumn($metricName));
    }

    private function passesFilter(float $value, array $filter): bool
    {
        $limit = $filter['value'];

        switch ($filter['operator']) {
            case 'gt':
                return $value > $limit;
            case 'gte':
                return $value >= $limit;
            case 'lt':
                return $value < $limit;
            case 'lte':
                return $value <= $limit;
            default:
                return $value == $limit;
        }
    }
}
