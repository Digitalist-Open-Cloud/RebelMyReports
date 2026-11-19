<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Config;
use Piwik\Container\StaticContainer;
use Piwik\Piwik;
use Piwik\Plugin\ControllerAdmin;
use Piwik\Plugin\Manager;
use Piwik\Plugins\SegmentEditor\API as SegmentEditorApi;
use Piwik\Site;

class Controller extends ControllerAdmin
{
    /**
     * The page that tells how to use the plugin, with the limits as they are set now.
     */
    public function documentation(): string
    {
        Piwik::checkUserHasSomeWriteAccess();

        $configuration = new Configuration();
        $limits = [
            Configuration::MAX_DIMENSIONS => $configuration->getMaxDimensions(),
            Configuration::MAX_ROWS => $configuration->getMaxRows(),
            Configuration::MAX_ROWS_SUBTABLE => $configuration->getMaxRowsInSubtable(),
            Configuration::MAX_EXECUTION_TIME => $configuration->getMaxExecutionTime(),
            Configuration::PREVIEW_MAX_EXECUTION_TIME => $configuration->getPreviewMaxExecutionTime(),
        ];

        $catalogue = StaticContainer::get(Catalogue::class);
        $dimensions = $catalogue->getDimensions();
        $examples = [];
        foreach (Examples::DEFINITIONS as $id => $definition) {
            $metrics = [];
            foreach ($definition['metrics'] as $name) {
                $metric = $catalogue->findMetric($name);
                $metrics[] = $metric ? $catalogue->getMetricLabel($metric) : $name;
            }
            if (isset($definition['calculated_metric'])) {
                $metrics[] = Piwik::translate("RebelMyReports_Example{$id}Metric");
            }

            $examples[] = [
                'id' => $id,
                'name' => Piwik::translate("RebelMyReports_Example{$id}Name"),
                'description' => Piwik::translate("RebelMyReports_Example{$id}Description"),
                'dimensions' => array_map(
                    fn (string $name) => isset($dimensions[$name]) ? $dimensions[$name]->getName() : $name,
                    $definition['dimensions'] ?? []
                ),
                'metrics' => $metrics,
            ];
        }

        return $this->renderTemplate('documentation', [
            'examples' => $examples,
            'limits' => $limits,
            'limitTitles' => SystemSettings::TITLES,
            'rearchiveMonths' => (int) str_replace('last', '', (string) Config::getInstance()->General['rearchive_reports_in_past_last_n_months']),
            'isSuperUser' => Piwik::hasUserSuperUserAccess(),
        ]);
    }

    /**
     * The page to mark the archived data of reports as outdated.
     */
    public function invalidate(): string
    {
        Piwik::checkUserHasSuperUserAccess();

        $reports = [];
        foreach ((new Model())->getAllReports() as $report) {
            $reports[] = [
                'idreport' => $report['idreport'],
                'name' => $report['name'],
                // The site name is stored HTML-encoded, like the report name.
                'site' => $report['idsite'] === 0 ? '' : Site::getNameFor($report['idsite']),
                'paused' => $report['paused'],
            ];
        }

        $segments = [];
        if (Manager::getInstance()->isPluginActivated('SegmentEditor')) {
            foreach (SegmentEditorApi::getInstance()->getAll() as $segment) {
                if ($segment['auto_archive']) {
                    $segments[] = ['name' => $segment['name'], 'definition' => $segment['definition']];
                }
            }
        }

        return $this->renderTemplate('invalidate', ['reports' => $reports, 'segments' => $segments]);
    }
}
