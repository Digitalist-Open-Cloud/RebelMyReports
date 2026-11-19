<?php

namespace Piwik\Plugins\RebelMyReports\Archiving;

/**
 * The value groups of a report: each gives some values of a dimension one name, for example
 * the countries "se", "no" and "dk" the name "Nordics". The first group a value matches
 * decides, and a value no group matches stays as it is.
 */
class ValueGroups
{
    // Operator => the operator of a segment that matches the same values.
    public const OPERATORS = ['equals' => '==', 'contains' => '=@', 'starts' => '=^', 'ends' => '=$'];

    /** @var array<int, array{dimension: string, name: string, operator: string, values: string[]}> */
    private array $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Returns SQL that gives the group name for the values a rule matches, and otherwise
     * the selected value.
     */
    public function toSql(string $dimensionId, string $select): string
    {
        $cases = '';
        foreach ($this->getRules($dimensionId) as $rule) {
            $conditions = [];
            foreach ($rule['values'] as $value) {
                if ($rule['operator'] === 'equals') {
                    $conditions[] = "$select = " . $this->literal($value);
                    continue;
                }

                $pattern = addcslashes($value, '\\%_');
                $pattern = ($rule['operator'] === 'starts' ? '' : '%') . $pattern . ($rule['operator'] === 'ends' ? '' : '%');
                $conditions[] = "$select LIKE " . $this->literal($pattern);
            }

            $cases .= ' WHEN ' . implode(' OR ', $conditions) . ' THEN ' . $this->literal($rule['name']);
        }

        return $cases === '' ? $select : "CASE$cases ELSE $select END";
    }

    public function isGroup(string $dimensionId, string $value): bool
    {
        return in_array($value, array_column($this->getRules($dimensionId), 'name'), true);
    }

    /**
     * Returns the condition of a segment for the visits of a group. A value that an earlier
     * group took is not left out of it.
     */
    public function getSegmentCondition(string $dimensionId, string $group, string $segmentName): string
    {
        $conditions = [];
        foreach ($this->getRules($dimensionId) as $rule) {
            if ($rule['name'] === $group) {
                foreach ($rule['values'] as $value) {
                    $conditions[] = $segmentName . self::OPERATORS[$rule['operator']] . urlencode($value);
                }
            }
        }

        return implode(',', $conditions);
    }

    private function getRules(string $dimensionId): array
    {
        return array_filter($this->rules, fn (array $rule) => $rule['dimension'] === $dimensionId);
    }

    /**
     * A string as a hex literal, so that nothing a user typed is part of the query text.
     */
    private function literal(string $value): string
    {
        return '_utf8mb4 0x' . bin2hex($value);
    }
}
