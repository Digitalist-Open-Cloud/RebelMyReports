<?php

namespace Piwik\Plugins\RebelMyReports\Visualizations;

use Piwik\API\Request as ApiRequest;
use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\DataTable;
use Piwik\DataTable\Filter\CalculateEvolutionFilter;
use Piwik\Metrics;
use Piwik\Metrics\Formatter;
use Piwik\Piwik;
use Piwik\Plugin\ViewDataTable;
use Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph\Evolution;
use Piwik\Plugins\RebelMyReports\Catalogue;
use Piwik\Plugins\RebelMyReports\ReportManager;
use Piwik\Request;
use Piwik\Segment;

/**
 * Shows one metric of a report as its value, the change since the previous period, the value
 * of each period and segment it is compared with and a line of the last periods with the value of each point.
 */
class KeyFigure extends ViewDataTable
{
    public const ID = 'rebelKeyFigure';

    /**
     * Keeps this visualization out of the footer icons of other reports.
     */
    public static function canDisplayViewDataTable(ViewDataTable $view)
    {
        return false;
    }

    public function render()
    {
        $request = Request::fromRequest();
        $idSite = $request->getIntegerParameter('idSite');
        $idMyReport = $request->getIntegerParameter('idMyReport', 0);
        $period = $request->getStringParameter('period');

        $report = StaticContainer::get(ReportManager::class)->getReport($idSite, $idMyReport);
        $catalogue = StaticContainer::get(Catalogue::class);
        $column = reset($report['metrics']);
        $metric = $catalogue->getMetrics()[$column] ?? null;
        if (!$metric) {
            return '';
        }

        [$lastPeriods] = Evolution::getDateRangeAndLastN($period, $request->getStringParameter('date'), null, $idSite);
        $points = $this->getPoints($idMyReport, $period, $lastPeriods, $column, $metric);
        $current = end($points) ?: ['label' => '', 'value' => 0, 'formatted' => '0'];
        $previous = count($points) > 1 ? $points[count($points) - 2] : null;

        $label = $catalogue->getMetricLabel($metric);
        $periods = [[$period, $request->getStringParameter('date')]];
        $compareDates = $request->getArrayParameter('compareDates', []);
        foreach ($request->getArrayParameter('comparePeriods', []) as $index => $comparePeriod) {
            if (isset($compareDates[$index])) {
                $periods[] = [(string) $comparePeriod, (string) $compareDates[$index]];
            }
        }
        $segments = array_merge([null], Common::unsanitizeInputValues($request->getArrayParameter('compareSegments', [])));

        $comparisons = [];
        foreach ($segments as $segment) {
            $segmentName = $segment === null ? '' : (new Segment($segment, [$idSite]))->getStoredSegmentName($idSite);
            foreach ($periods as $index => [$comparePeriod, $compareDate]) {
                if ($segment === null && $index === 0) {
                    continue;
                }

                foreach ($this->getPoints($idMyReport, $comparePeriod, $compareDate, $column, $metric, $segment) as $point) {
                    if ($segment !== null) {
                        $point['label'] = $index === 0 ? $segmentName : $segmentName . ', ' . $point['label'];
                    }
                    $comparisons[] = $point + ['evolution' => $this->getEvolution($current, $point, $column, $label)];
                }
            }
        }

        $props = [
            'id-my-report' => $idMyReport,
            'report-title' => $this->config->title,
            'metric-name' => $label,
            'value' => $current['formatted'],
            'points' => $points,
            'evolution' => $previous ? $this->getEvolution($current, $previous, $column, $label) : null,
            'comparisons' => $comparisons,
        ];

        $attributes = '';
        foreach ($props as $name => $value) {
            $attributes .= sprintf(' %s="%s"', $name, htmlspecialchars(json_encode($value), ENT_QUOTES));
        }
        $html = '<div vue-entry="RebelMyReports.MyReportKeyFigure"' . $attributes . '></div>';

        if ($request->getIntegerParameter('widget', 0)) {
            return $html;
        }

        // The report name is stored HTML-encoded.
        return '<div class="card"><div class="card-content"><h2 class="card-title">' . $this->config->title . '</h2>'
            . $html . '</div></div>';
    }

    /**
     * @param array{label: string, value: float|int, formatted: string} $current
     * @param array{label: string, value: float|int, formatted: string} $earlier
     * @return array the change from the earlier value to the current one
     */
    private function getEvolution(array $current, array $earlier, string $column, string $metricLabel): array
    {
        $percent = CalculateEvolutionFilter::calculate($current['value'], $earlier['value'], 1);

        return [
            'percent' => $percent,
            'trend' => $current['value'] - $earlier['value'],
            'isLowerValueBetter' => (bool) Metrics::isLowerValueBetter($column),
            'tooltip' => Piwik::translate('General_EvolutionSummaryGeneric', [
                $current['formatted'] . ' ' . $metricLabel,
                $current['label'],
                $earlier['formatted'] . ' ' . $metricLabel,
                $earlier['label'],
                $percent,
            ]),
        ];
    }

    /**
     * Returns the value of the metric for each period of the date, which is one date or several.
     *
     * @param \Piwik\Plugin\Metric $metric
     * @param string|null $segment null for the segment of the request
     * @return array<int, array{label: string, value: float|int, formatted: string}>
     */
    private function getPoints(int $idMyReport, string $period, string $date, string $column, $metric, ?string $segment = null): array
    {
        $tables = ApiRequest::processRequest('RebelMyReports.getReportData', [
            'idMyReport' => $idMyReport,
            'period' => $period,
            'date' => $date,
            'format_metrics' => '0',
            'compare' => '0',
        ] + ($segment === null ? [] : ['segment' => $segment]));
        if ($tables instanceof DataTable) {
            $tables = [$tables];
        } else {
            $tables = $tables->getDataTables();
        }

        $formatter = new Formatter();
        $points = [];
        foreach ($tables as $table) {
            $row = $table->getFirstRow();
            $value = $row ? $row->getColumn($column) : 0;
            $value = is_numeric($value) ? $value + 0 : 0;

            $points[] = [
                'label' => $table->getMetadata('period')->getLocalizedShortString(),
                'value' => $value,
                'formatted' => (string) $metric->format($value, $formatter),
            ];
        }

        return $points;
    }
}
