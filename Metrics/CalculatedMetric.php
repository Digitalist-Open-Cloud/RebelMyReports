<?php

namespace Piwik\Plugins\RebelMyReports\Metrics;

use Piwik\DataTable\Row;
use Piwik\Metrics\Formatter;
use Piwik\Plugin\ProcessedMetric;

/**
 * A metric defined by a user as a formula over archived metrics. It is calculated when a
 * report is shown, from the archived values of the row, so it is also right for totals.
 */
class CalculatedMetric extends ProcessedMetric
{
    public const NAME_PREFIX = 'rebelcalc_';
    public const CATEGORY = 'RebelMyReports_CalculatedMetrics';

    public const FORMAT_NUMBER = 'number';
    public const FORMAT_PERCENT = 'percent';
    public const FORMAT_DURATION = 'duration';
    public const FORMAT_MONEY = 'money';
    public const FORMATS = [self::FORMAT_NUMBER, self::FORMAT_PERCENT, self::FORMAT_DURATION, self::FORMAT_MONEY];

    private array $definition;
    private Formula $formula;

    /**
     * @param array $definition idmetric, idsite, name, formula and format
     */
    public function __construct(array $definition)
    {
        $this->definition = $definition;
        $this->formula = new Formula($definition['formula']);
    }

    public static function getNameOf(int $idMetric): string
    {
        return self::NAME_PREFIX . $idMetric;
    }

    public function getName()
    {
        return self::getNameOf($this->definition['idmetric']);
    }

    public function getTranslatedName()
    {
        return $this->definition['name'];
    }

    public function getCategoryId()
    {
        return self::CATEGORY;
    }

    public function getDocumentation()
    {
        return $this->definition['formula'];
    }

    public function getDependentMetrics()
    {
        return $this->formula->getMetricNames();
    }

    public function compute(Row $row)
    {
        $values = [];
        foreach ($this->formula->getMetricNames() as $name) {
            $values[$name] = $this->getMetric($row, $name);
        }

        return $this->formula->evaluate($values);
    }

    public function format($value, Formatter $formatter)
    {
        switch ($this->definition['format']) {
            case self::FORMAT_PERCENT:
                return $formatter->getPrettyPercentFromQuotient($value);
            case self::FORMAT_DURATION:
                return $formatter->getPrettyTimeFromSeconds((int) round($value), true);
            case self::FORMAT_MONEY:
                return $formatter->getPrettyMoney($value, $this->definition['idsite']);
            default:
                return $formatter->getPrettyNumber($value, 2);
        }
    }
}
