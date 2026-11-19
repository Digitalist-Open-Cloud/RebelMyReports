<?php

namespace Piwik\Plugins\RebelMyReports;

use Exception;
use InvalidArgumentException;
use Piwik\Cache;
use Piwik\Columns\Dimension;
use Piwik\Columns\MetricsList;
use Piwik\DataAccess\LogQueryBuilder\JoinGenerator;
use Piwik\DataAccess\LogQueryBuilder\JoinTables;
use Piwik\Piwik;
use Piwik\Plugin\ArchivedMetric;
use Piwik\Plugin\ComputedMetric;
use Piwik\Plugin\LogTablesProvider;
use Piwik\Plugin\Metric;
use Piwik\Plugins\RebelMyReports\Metrics\CalculatedMetric;
use Piwik\Plugins\RebelMyReports\Metrics\MetricModel;
use Piwik\Request;

/**
 * The dimensions and metrics a report can be built from: Matomo's own dimension and metric
 * lists and the calculated metrics. All depend on the site of the current request (custom
 * dimensions, goals, calculated metrics).
 */
class Catalogue
{
    private const CALCULATED_METRICS_CACHE_ID = 'RebelMyReports.calculatedMetrics.';

    private LogTablesProvider $logTablesProvider;
    private MetricModel $metricModel;
    private Configuration $configuration;

    public function __construct(LogTablesProvider $logTablesProvider, MetricModel $metricModel, Configuration $configuration)
    {
        $this->configuration = $configuration;
        $this->logTablesProvider = $logTablesProvider;
        $this->metricModel = $metricModel;
    }

    /**
     * @return Dimension[] indexed by dimension ID
     */
    public function getDimensions(): array
    {
        $dimensions = [];

        foreach (Dimension::getAllDimensions() as $dimension) {
            $table = $dimension->getDbTableName();

            if ($table && $dimension->getSqlSegment() && $dimension->getName() && $this->logTablesProvider->getLogTable($table)) {
                $dimensions[$dimension->getId()] = $dimension;
            }
        }

        return $dimensions;
    }

    /**
     * @return Metric[] indexed by metric name
     */
    public function getMetrics(): array
    {
        $metrics = [];

        foreach (MetricsList::get()->getMetrics() as $metric) {
            if ($this->getMetricTables($metric) !== null) {
                $metrics[$metric->getName()] = $metric;
            }
        }

        foreach ($this->getCalculatedMetrics() as $metric) {
            if ($this->getMetricTables($metric) !== null) {
                $metrics[$metric->getName()] = $metric;
            }
        }

        return $metrics;
    }

    /**
     * Returns a metric of Matomo or a calculated metric of the site of the request.
     */
    public function findMetric(string $name): ?Metric
    {
        if (strpos($name, CalculatedMetric::NAME_PREFIX) === 0) {
            return $this->getCalculatedMetrics()[$name] ?? null;
        }

        return MetricsList::get()->getMetric($name) ?: null;
    }

    /**
     * @return CalculatedMetric[] the calculated metrics of the site of the request, by name
     */
    private function getCalculatedMetrics(): array
    {
        $idSite = Request::fromRequest()->getIntegerParameter('idSite', 0);
        $cache = Cache::getTransientCache();
        $cacheId = self::CALCULATED_METRICS_CACHE_ID . $idSite;
        if ($cache->contains($cacheId)) {
            return $cache->fetch($cacheId);
        }

        $metrics = [];

        foreach ($idSite > 0 ? $this->metricModel->getMetrics($idSite) : [] as $definition) {
            try {
                $metric = new CalculatedMetric($definition);
            } catch (InvalidArgumentException $e) {
                // A stored formula that is not valid is left out.
                continue;
            }
            $metrics[$metric->getName()] = $metric;
        }

        $cache->save($cacheId, $metrics);

        return $metrics;
    }

    /**
     * Makes the catalogue read the calculated metrics again after one was changed.
     */
    public function forgetCalculatedMetrics(int $idSite): void
    {
        Cache::getTransientCache()->delete(self::CALCULATED_METRICS_CACHE_ID . $idSite);
    }

    public function getMetricLabel(Metric $metric): string
    {
        $label = $metric->getTranslatedName();

        // Some sum metrics carry the bare dimension name, which reads like the dimension itself.
        if (
            $metric instanceof ArchivedMetric
            && strpos($metric->getName(), ArchivedMetric::AGGREGATION_SUM_PREFIX) === 0
            && stripos($label, 'total') === false
        ) {
            $label = Piwik::translate('General_Total') . ' ' . $label;
        }

        return $label;
    }

    /**
     * Returns the log tables a metric reads from, or null when it cannot be queried from logs.
     *
     * @return string[]|null
     */
    public function getMetricTables(Metric $metric): ?array
    {
        if ($metric instanceof ArchivedMetric) {
            $table = $metric->getDbTableName();

            return $table && $this->logTablesProvider->getLogTable($table) ? [$table] : null;
        }

        if (!$metric instanceof ComputedMetric && !$metric instanceof CalculatedMetric) {
            return null;
        }

        $tables = [];
        foreach ($metric->getDependentMetrics() as $name) {
            // A formula can only use archived metrics, which also rules out formulas that
            // use each other.
            $dependent = MetricsList::get()->getMetric($name);
            if ($metric instanceof CalculatedMetric && !$dependent instanceof ArchivedMetric) {
                return null;
            }
            $dependentTables = $dependent ? $this->getMetricTables($dependent) : null;

            if ($dependentTables === null) {
                return null;
            }

            $tables = array_merge($tables, $dependentTables);
        }

        return array_values(array_unique($tables));
    }

    /**
     * Whether the dimension only exists for the site of the request, like a custom dimension.
     */
    public function isSiteSpecificDimension(Dimension $dimension): bool
    {
        $id = $dimension->getId();

        return strpos($id, 'CustomDimension.') === 0 || preg_match('/^Goals\.Goal[A-Za-z_]+\d+$/', $id) === 1;
    }

    /**
     * Whether the metric only exists for the site of the request, like the metrics of a goal.
     */
    public function isSiteSpecificMetric(Metric $metric): bool
    {
        if ($metric instanceof ArchivedMetric) {
            return $this->isSiteSpecificDimension($metric->getDimension());
        }

        if ($metric instanceof CalculatedMetric) {
            return true;
        }

        if ($metric instanceof ComputedMetric) {
            foreach ($metric->getDependentMetrics() as $name) {
                $dependent = MetricsList::get()->getMetric($name);
                if ($dependent && $this->isSiteSpecificMetric($dependent)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string[] $dimensionIds
     * @param string[] $metricNames
     * @throws Exception if an ID is unknown or the selection cannot be queried together
     */
    public function assertCombinable(array $dimensionIds, array $metricNames): void
    {
        $maxDimensions = $this->configuration->getMaxDimensions();
        if (count($dimensionIds) > $maxDimensions) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorTooManyDimensions', $maxDimensions));
        }

        if (empty($metricNames)) {
            throw new Exception(Piwik::translate('RebelMyReports_ErrorNoMetric'));
        }

        $dimensions = $this->getDimensions();
        $metrics = $this->getMetrics();
        $tables = [];

        foreach ($dimensionIds as $id) {
            if (!is_string($id) || !isset($dimensions[$id])) {
                throw new Exception(Piwik::translate('RebelMyReports_ErrorUnknownDimension', $this->printable($id)));
            }
            $tables[] = $dimensions[$id]->getDbTableName();
        }

        foreach ($metricNames as $name) {
            if (!is_string($name) || !isset($metrics[$name])) {
                throw new Exception(Piwik::translate('RebelMyReports_ErrorUnknownMetric', $this->printable($name)));
            }
            $tables = array_merge($tables, $this->getMetricTables($metrics[$name]));
        }

        $this->assertJoinable(array_values(array_unique($tables)));
    }

    /**
     * @param string[] $tables
     */
    private function assertJoinable(array $tables): void
    {
        if (count($tables) < 2) {
            return;
        }

        $error = Piwik::translate('RebelMyReports_ErrorNotCombinable', implode(', ', $tables));

        try {
            $generator = new JoinGenerator(new JoinTables($this->logTablesProvider, $tables));
            $generator->generate();
        } catch (Exception $e) {
            throw new Exception($error);
        }

        // The generator skips a table it finds no join for instead of failing.
        foreach ($tables as $table) {
            if (strpos($generator->getJoinString(), " AS $table") === false) {
                throw new Exception($error);
            }
        }
    }

    /**
     * @param mixed $value
     */
    private function printable($value): string
    {
        return is_string($value) ? $value : gettype($value);
    }
}
