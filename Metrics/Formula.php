<?php

namespace Piwik\Plugins\RebelMyReports\Metrics;

use InvalidArgumentException;

/**
 * An arithmetic formula over metric values, for example `(pageviews - bounce_count) / nb_visits`.
 * It knows numbers, metric names, the operators + - * / and parentheses. Nothing is ever
 * evaluated as code.
 */
class Formula
{
    private const PRECEDENCE = ['+' => 1, '-' => 1, '*' => 2, '/' => 2];

    /** @var array<int, array{type: string, value: string|float}> the formula in reverse Polish notation */
    private array $tokens;

    /**
     * @throws InvalidArgumentException if the formula is not valid
     */
    public function __construct(string $formula)
    {
        $this->tokens = $this->toReversePolish($this->tokenize($formula));

        // A dry run finds formulas like "1 +" or "2 3".
        $this->evaluate(array_fill_keys($this->getMetricNames(), 1));
    }

    /**
     * @return string[] the names of the metrics the formula uses
     */
    public function getMetricNames(): array
    {
        $names = [];
        foreach ($this->tokens as $token) {
            if ($token['type'] === 'metric') {
                $names[$token['value']] = true;
            }
        }

        return array_keys($names);
    }

    /**
     * @param array<string, mixed> $values metric values by name. A missing or non-numeric value
     *                                     counts as 0.
     * @return int|float 0 when the formula divides by zero
     */
    public function evaluate(array $values)
    {
        $stack = [];

        foreach ($this->tokens as $token) {
            if ($token['type'] === 'number') {
                $stack[] = $token['value'];
                continue;
            }
            if ($token['type'] === 'metric') {
                $value = $values[$token['value']] ?? 0;
                $stack[] = is_numeric($value) ? $value + 0 : 0;
                continue;
            }

            if (count($stack) < 2) {
                throw new InvalidArgumentException('The formula is not complete.');
            }
            $right = array_pop($stack);
            $left = array_pop($stack);

            switch ($token['value']) {
                case '+':
                    $stack[] = $left + $right;
                    break;
                case '-':
                    $stack[] = $left - $right;
                    break;
                case '*':
                    $stack[] = $left * $right;
                    break;
                default:
                    if ($right == 0) {
                        return 0;
                    }
                    $stack[] = $left / $right;
            }
        }

        if (count($stack) !== 1) {
            throw new InvalidArgumentException('The formula is not complete.');
        }

        return $stack[0];
    }

    /**
     * @return array<int, array{type: string, value: string|float}>
     */
    private function tokenize(string $formula): array
    {
        $pattern = '/\s*(?:(?<number>\d+(?:\.\d+)?)|(?<metric>[A-Za-z_][A-Za-z0-9_]*)|(?<operator>[-+*\/])|(?<paren>[()]))/A';
        $tokens = [];
        $offset = 0;
        $formula = rtrim($formula);

        while ($offset < strlen($formula)) {
            if (!preg_match($pattern, $formula, $match, PREG_UNMATCHED_AS_NULL, $offset)) {
                throw new InvalidArgumentException(sprintf('Unexpected "%s" in the formula.', substr($formula, $offset, 10)));
            }
            $offset += strlen($match[0]);

            if ($match['number'] !== null) {
                $tokens[] = ['type' => 'number', 'value' => (float) $match['number']];
            } elseif ($match['metric'] !== null) {
                $tokens[] = ['type' => 'metric', 'value' => $match['metric']];
            } elseif ($match['operator'] !== null) {
                $tokens[] = ['type' => 'operator', 'value' => $match['operator']];
            } else {
                $tokens[] = ['type' => 'paren', 'value' => $match['paren']];
            }
        }

        if (empty($tokens)) {
            throw new InvalidArgumentException('The formula is empty.');
        }

        return $tokens;
    }

    /**
     * Shunting-yard: orders the tokens so they can be evaluated left to right with a stack.
     *
     * @param array<int, array{type: string, value: string|float}> $tokens
     * @return array<int, array{type: string, value: string|float}>
     */
    private function toReversePolish(array $tokens): array
    {
        $output = [];
        $operators = [];

        foreach ($tokens as $token) {
            if ($token['type'] === 'number' || $token['type'] === 'metric') {
                $output[] = $token;
            } elseif ($token['type'] === 'operator') {
                while (
                    $operators
                    && end($operators)['type'] === 'operator'
                    && self::PRECEDENCE[end($operators)['value']] >= self::PRECEDENCE[$token['value']]
                ) {
                    $output[] = array_pop($operators);
                }
                $operators[] = $token;
            } elseif ($token['value'] === '(') {
                $operators[] = $token;
            } else {
                while ($operators && end($operators)['value'] !== '(') {
                    $output[] = array_pop($operators);
                }
                if (!$operators) {
                    throw new InvalidArgumentException('The formula has a ")" without a "(".');
                }
                array_pop($operators);
            }
        }

        while ($operators) {
            $operator = array_pop($operators);
            if ($operator['type'] === 'paren') {
                throw new InvalidArgumentException('The formula has a "(" without a ")".');
            }
            $output[] = $operator;
        }

        return $output;
    }
}
