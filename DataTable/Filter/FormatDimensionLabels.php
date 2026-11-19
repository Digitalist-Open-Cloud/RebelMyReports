<?php

namespace Piwik\Plugins\RebelMyReports\DataTable\Filter;

use Piwik\Columns\Dimension;
use Piwik\DataTable;
use Piwik\DataTable\BaseFilter;
use Piwik\Metrics\Formatter;
use Piwik\Piwik;
use Piwik\Plugins\RebelMyReports\Archiving\ValueGroups;

/**
 * Replaces the archived dimension values of a report with readable labels and adds the
 * segment that matches each row. Subtables are formatted with the next dimension.
 */
class FormatDimensionLabels extends BaseFilter
{
    /** @var Dimension[] */
    private array $dimensions;
    private int $idSite;
    private int $level;
    private ValueGroups $valueGroups;

    /**
     * @param Dimension[] $dimensions the dimensions of the report, in order
     * @param int $level index of the dimension the rows of $table belong to
     * @param array[] $valueGroups the value groups of the report. Their names are labels already.
     */
    public function __construct(DataTable $table, array $dimensions, int $idSite, int $level = 0, array $valueGroups = [])
    {
        parent::__construct($table);

        $this->valueGroups = new ValueGroups($valueGroups);
        $this->dimensions = array_values($dimensions);
        $this->idSite = $idSite;
        $this->level = $level;
    }

    /**
     * @param DataTable $table
     */
    public function filter($table)
    {
        if (empty($this->dimensions)) {
            // A report without dimension has a single row, like the "get" reports of core.
            $table->deleteColumn('label');
            return;
        }

        // The segment of a row includes its parent rows, which are unknown for a lone subtable.
        $this->format($table, $this->level, $this->level === 0 ? '' : null);
    }

    private function format(DataTable $table, int $level, ?string $parentSegment): void
    {
        if (!isset($this->dimensions[$level])) {
            return;
        }

        $dimension = $this->dimensions[$level];
        $formatter = new Formatter();

        foreach ($table->getRowsWithoutSummaryRow() as $row) {
            $value = (string) $row->getColumn('label');
            $isGroup = $this->valueGroups->isGroup($dimension->getId(), $value);
            $segment = $this->getSegment($dimension, $value, $parentSegment, $isGroup);

            if ($segment !== null) {
                $row->setMetadata('segment', $segment);
            }
            if (!$isGroup) {
                $row->setColumn('label', $this->getLabel($dimension, $value, $formatter));
            }

            $subtable = $row->getSubtable();
            if ($subtable) {
                $this->format($subtable, $level + 1, $segment);
            }
        }
    }

    private function getLabel(Dimension $dimension, string $value, Formatter $formatter): string
    {
        if ($value === '') {
            return Piwik::translate('General_NotDefined', $dimension->getName());
        }

        // Binary values are archived as hex strings.
        if ($dimension->getType() === Dimension::TYPE_BINARY) {
            return $value;
        }

        return (string) $dimension->formatValue($value, $this->idSite, $formatter);
    }

    private function getSegment(Dimension $dimension, string $value, ?string $parentSegment, bool $isGroup): ?string
    {
        $name = $dimension->getSegmentName();
        if ($parentSegment === null || !$name || $value === '') {
            return null;
        }

        $condition = $isGroup
            ? $this->valueGroups->getSegmentCondition($dimension->getId(), $value, $name)
            : $name . '==' . urlencode($value);

        return ($parentSegment === '' ? '' : $parentSegment . ';') . $condition;
    }
}
