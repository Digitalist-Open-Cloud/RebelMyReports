<?php

namespace Piwik\Plugins\RebelMyReports;

use Exception;
use Piwik\Context;
use Piwik\Piwik;
use Piwik\Plugins\RebelMyReports\Metrics\CalculatedMetric;
use Piwik\Plugins\RebelMyReports\Metrics\MetricManager;
use Piwik\Plugins\RebelMyReports\Metrics\MetricModel;

/**
 * Copies reports and calculated metrics, within a website or to another one.
 */
class Duplicator
{
    private ReportManager $reportManager;
    private MetricManager $metricManager;
    private MetricModel $metricModel;

    public function __construct(ReportManager $reportManager, MetricManager $metricManager, MetricModel $metricModel)
    {
        $this->reportManager = $reportManager;
        $this->metricManager = $metricManager;
        $this->metricModel = $metricModel;
    }

    /**
     * Copies the report to the website. The calculated metrics it shows are copied along,
     * unless the website already has the same metric.
     *
     * @return int the ID of the new report
     * @throws Exception if the report uses something the website does not have
     */
    public function duplicateReport(array $report, int $idDestinationSite): int
    {
        $isSameSite = $report['idsite'] === $idDestinationSite;

        // Dimensions, metrics and pages depend on the website of the request.
        return Context::executeWithQueryParameters(['idSite' => $idDestinationSite], function () use ($report, $idDestinationSite, $isSameSite) {
            $metrics = $report['metrics'];
            if (!$isSameSite) {
                $metrics = array_map(fn (string $name) => $this->getMetricNameInSite($name, $report['idsite'], $idDestinationSite), $metrics);
            }
            $newNames = array_combine($report['metrics'], $metrics) + ['' => ''];

            $pages = $this->reportManager->getPages($idDestinationSite);
            $categoryId = GetReportData::getCategoryIdOf($report);
            $hasCategory = isset($pages[$categoryId]);
            $hasPage = $hasCategory && isset($pages[$categoryId]['pages'][$report['subcategory']]);

            return $this->reportManager->addReport($idDestinationSite, [
                'name' => $isSameSite ? $this->getCopyName($report['name']) : $report['name'],
                'description' => $report['description'],
                'report_type' => $report['report_type'],
                'dimensions' => $report['dimensions'],
                'metrics' => $metrics,
                'segment_filter' => $report['segment_filter'],
                'value_groups' => $report['value_groups'],
                'visualization' => $report['visualization'],
                // A page of the website the report comes from does not exist in the other one.
                'category' => $hasCategory ? $report['category'] : '',
                'subcategory' => $hasPage ? $report['subcategory'] : '',
                'sort_metric' => $newNames[$report['sort_metric']],
                'sort_order' => $report['sort_order'],
                'row_limit' => $report['row_limit'],
                'metric_filters' => array_map(
                    fn (array $filter) => ['metric' => $newNames[$filter['metric']]] + $filter,
                    $report['metric_filters']
                ),
            ], false);
        });
    }

    /**
     * @return int the ID of the new metric
     * @throws Exception if the formula uses a metric the website does not have
     */
    public function duplicateMetric(array $metric, int $idDestinationSite): int
    {
        $name = $metric['idsite'] === $idDestinationSite ? $this->getCopyName($metric['name']) : $metric['name'];

        return Context::executeWithQueryParameters(['idSite' => $idDestinationSite], function () use ($metric, $name, $idDestinationSite) {
            return $this->metricManager->addMetric($idDestinationSite, $name, $metric['formula'], $metric['format']);
        });
    }

    /**
     * Names are limited to 100 characters, so a long name is shortened to fit "(copy)".
     */
    private function getCopyName(string $name): string
    {
        $suffixLength = mb_strlen(Piwik::translate('RebelMyReports_CopyOf', ''));

        return Piwik::translate('RebelMyReports_CopyOf', mb_substr($name, 0, 100 - $suffixLength));
    }

    /**
     * Returns the name to use for a metric of one website in a report of another: a
     * calculated metric becomes the same metric of the other website, which is created if
     * that website does not have it yet.
     */
    private function getMetricNameInSite(string $metricName, int $idSite, int $idDestinationSite): string
    {
        if (strpos($metricName, CalculatedMetric::NAME_PREFIX) !== 0) {
            return $metricName;
        }

        $metric = $this->metricManager->getMetric($idSite, (int) substr($metricName, strlen(CalculatedMetric::NAME_PREFIX)));

        foreach ($this->metricModel->getMetrics($idDestinationSite) as $existing) {
            $isSame = $existing['name'] === $metric['name']
                && $existing['formula'] === $metric['formula']
                && $existing['format'] === $metric['format'];
            if ($isSame) {
                return CalculatedMetric::getNameOf($existing['idmetric']);
            }
        }

        return CalculatedMetric::getNameOf($this->duplicateMetric($metric, $idDestinationSite));
    }
}
