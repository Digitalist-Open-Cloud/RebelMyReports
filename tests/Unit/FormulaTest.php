<?php

namespace Piwik\Plugins\RebelMyReports\tests\Unit;

use PHPUnit\Framework\TestCase;
use Piwik\Plugins\RebelMyReports\Metrics\Formula;

/**
 * @group RebelMyReports
 * @group Plugins
 */
class FormulaTest extends TestCase
{
    /**
     * @dataProvider getFormulas
     */
    public function testEvaluatesFormulas(string $formula, $expected)
    {
        $values = ['nb_visits' => 10, 'pageviews' => 25, 'bounce_count' => 4, 'goal_1_conversion' => 2];

        $this->assertEquals($expected, (new Formula($formula))->evaluate($values));
    }

    public function getFormulas(): array
    {
        return [
            'a metric' => ['nb_visits', 10],
            'a number' => ['2.5', 2.5],
            'ratio' => ['pageviews / nb_visits', 2.5],
            'multiplication before addition' => ['1 + 2 * 3', 7],
            'left to right' => ['10 - 4 - 3', 3],
            'parentheses' => ['(pageviews - bounce_count) / (nb_visits - 3)', 3],
            'metric names with digits' => ['goal_1_conversion / nb_visits * 100', 20],
            'no spaces' => ['pageviews/nb_visits', 2.5],
            'division by zero is zero' => ['pageviews / (nb_visits - 10)', 0],
            'missing metric counts as zero' => ['pageviews + unknown_metric', 25],
        ];
    }

    public function testListsTheMetricsItUses()
    {
        $formula = new Formula('(pageviews - bounce_count) / nb_visits + pageviews');

        $this->assertSame(['pageviews', 'bounce_count', 'nb_visits'], $formula->getMetricNames());
    }

    /**
     * @dataProvider getInvalidFormulas
     */
    public function testRejectsInvalidFormulas(string $formula)
    {
        $this->expectException(\InvalidArgumentException::class);

        new Formula($formula);
    }

    public function getInvalidFormulas(): array
    {
        return [
            'empty' => ['   '],
            'missing operand' => ['nb_visits +'],
            'two operands' => ['nb_visits pageviews'],
            'unclosed parenthesis' => ['(nb_visits + 1'],
            'unopened parenthesis' => ['nb_visits + 1)'],
            'sql' => ['nb_visits; DROP TABLE log_visit'],
            'function call' => ['sleep(10)'],
            'quotes' => ["nb_visits + '1'"],
            'power operator' => ['nb_visits ^ 2'],
        ];
    }
}
