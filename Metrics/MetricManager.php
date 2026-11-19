<?php

namespace Piwik\Plugins\RebelMyReports\Metrics;

use Exception;
use InvalidArgumentException;
use Piwik\Columns\MetricsList;
use Piwik\Piwik;
use Piwik\Plugin\ArchivedMetric;
use Piwik\Plugins\RebelMyReports\Catalogue;
use Piwik\Plugins\RebelMyReports\ReportManager;

/**
 * Validates, stores and removes calculated metrics.
 */
class MetricManager
{
    private const MAX_NAME_LENGTH = 100;
    private const MAX_FORMULA_LENGTH = 1000;

    private MetricModel $model;
    private Catalogue $catalogue;
    private ReportManager $reportManager;

    public function __construct(MetricModel $model, Catalogue $catalogue, ReportManager $reportManager)
    {
        $this->model = $model;
        $this->catalogue = $catalogue;
        $this->reportManager = $reportManager;
    }

    /**
     * @throws Exception if the site has no such metric
     */
    public function getMetric(int $idSite, int $idMetric): array
    {
        $metric = $this->model->getMetric($idMetric);

        if (!$metric || $metric['idsite'] !== $idSite) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorMetricNotFound', $idMetric));
        }

        return $metric;
    }

    /**
     * @throws Exception if the definition is not valid
     */
    public function addMetric(int $idSite, string $name, string $formula, string $format): int
    {
        [$name, $formula] = $this->validate($name, $formula, $format);

        $idMetric = $this->model->addMetric($idSite, $name, $formula, $format);
        $this->catalogue->forgetCalculatedMetrics($idSite);

        return $idMetric;
    }

    /**
     * Reports that show the metric are built again when its formula changes.
     *
     * @throws Exception if the definition is not valid
     */
    public function updateMetric(array $metric, string $name, string $formula, string $format): void
    {
        [$name, $formula] = $this->validate($name, $formula, $format);

        $this->model->updateMetric($metric['idmetric'], $name, $formula, $format);
        $this->catalogue->forgetCalculatedMetrics($metric['idsite']);

        if ($formula !== $metric['formula']) {
            $metricName = CalculatedMetric::getNameOf($metric['idmetric']);
            foreach ($this->reportManager->getReportsUsingMetric($metric['idsite'], $metricName) as $report) {
                $this->reportManager->rebuildReport($report);
            }
        }
    }

    /**
     * @throws Exception if a report still shows the metric
     */
    public function deleteMetric(array $metric): void
    {
        $reports = $this->reportManager->getReportsUsingMetric($metric['idsite'], CalculatedMetric::getNameOf($metric['idmetric']));
        if ($reports) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorMetricInUse', implode(', ', array_column($reports, 'name'))));
        }

        $this->model->deleteMetric($metric['idmetric']);
        $this->catalogue->forgetCalculatedMetrics($metric['idsite']);
    }

    /**
     * @return string[] the normalized name and formula
     */
    private function validate(string $name, string $formula, string $format): array
    {
        $name = trim($name);
        if ($name === '' || mb_strlen($name) > self::MAX_NAME_LENGTH) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorMetricName', self::MAX_NAME_LENGTH));
        }

        if (!in_array($format, CalculatedMetric::FORMATS, true)) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorMetricFormat', implode(', ', CalculatedMetric::FORMATS)));
        }

        $formula = trim($formula);
        if (strlen($formula) > self::MAX_FORMULA_LENGTH) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorFormula', $formula));
        }

        try {
            $parsed = new Formula($formula);
        } catch (InvalidArgumentException $e) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorFormula', $e->getMessage()));
        }

        $selectable = $this->catalogue->getMetrics();
        foreach ($parsed->getMetricNames() as $metricName) {
            $metric = MetricsList::get()->getMetric($metricName);
            if (!$metric instanceof ArchivedMetric || !isset($selectable[$metricName])) {
                throw new Exception(Piwik::translate('RebelMyReports_ErrorFormulaMetric', $metricName));
            }
        }

        if (empty($parsed->getMetricNames())) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorFormulaNeedsMetric'));
        }

        return [$name, $formula];
    }
}
