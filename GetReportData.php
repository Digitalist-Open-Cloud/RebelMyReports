<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Columns\Dimension;
use Piwik\Container\StaticContainer;
use Piwik\DataTable;
use Piwik\Piwik;
use Piwik\Plugin\ProcessedMetric;
use Piwik\Plugin\Report;
use Piwik\Plugin\ViewDataTable;
use Piwik\Plugins\RebelMyReports\Archiving\ArchiveStatus;
use Piwik\Plugins\CoreVisualizations\Visualizations\HtmlTable;
use Piwik\Plugins\CoreVisualizations\Visualizations\JqplotGraph\Evolution;
use Piwik\Plugins\RebelMyReports\Visualizations\KeyFigure;
use Piwik\Request;

/**
 * The report of one user-defined report definition. Not in the Reports folder because there
 * is one instance per definition, added by the Report.addReports event.
 */
class GetReportData extends Report
{
    public const DEFAULT_CATEGORY = 'RebelMyReports_MyReports';
    public const OWN_PAGE_PREFIX = 'RebelMyReports_page_';

    private ?array $report = null;

    /** @var Dimension[] */
    private array $dimensions = [];

    /** @var string[] */
    private array $columns = [];

    public static function getCategoryIdOf(array $report): string
    {
        return $report['category'] !== '' ? $report['category'] : self::DEFAULT_CATEGORY;
    }

    /**
     * Returns the ID of the reporting page the report is shown on: the existing page it names,
     * or a page of its own.
     */
    public static function getSubcategoryIdOf(array $report): string
    {
        return $report['subcategory'] !== '' ? $report['subcategory'] : self::OWN_PAGE_PREFIX . $report['idreport'];
    }

    protected function init()
    {
        parent::init();

        $this->module = 'RebelMyReports';
        $this->action = 'getReportData';
        $this->categoryId = self::DEFAULT_CATEGORY;
        $this->name = Piwik::translate(self::DEFAULT_CATEGORY);

        // Core creates this class without arguments to handle a request for one report.
        $request = Request::fromRequest();
        $idSite = $request->getIntegerParameter('idSite', 0);
        $idMyReport = $request->getIntegerParameter('idMyReport', 0);

        if ($idSite > 0 && $idMyReport > 0) {
            try {
                $this->initFromReport(StaticContainer::get(ReportManager::class)->getReport($idSite, $idMyReport));
            } catch (\Exception $e) {
                // No such report for the site: the report stays disabled.
            }
        }
    }

    public function initFromReport(array $report): void
    {
        $catalogue = StaticContainer::get(Catalogue::class);
        $availableDimensions = $catalogue->getDimensions();
        $availableMetrics = $catalogue->getMetrics();

        $this->report = $report;
        $this->name = $report['name'];
        $this->documentation = $report['description'];
        $this->categoryId = self::getCategoryIdOf($report);
        $this->subcategoryId = self::getSubcategoryIdOf($report);
        $this->parameters = ['idMyReport' => $report['idreport']];
        $this->order = 100 + $report['position'];

        $this->dimensions = [];
        foreach ($report['dimensions'] as $id) {
            if (isset($availableDimensions[$id])) {
                $this->dimensions[] = $availableDimensions[$id];
            }
        }
        $this->dimension = $this->dimensions[0] ?? null;
        $this->actionToLoadSubTables = count($this->dimensions) > 1 ? $this->action : null;

        $this->metrics = [];
        $this->processedMetrics = [];
        $this->columns = [];
        foreach ($report['metrics'] as $name) {
            if (!isset($availableMetrics[$name])) {
                continue;
            }

            $this->columns[] = $name;
            if ($availableMetrics[$name] instanceof ProcessedMetric) {
                $this->processedMetrics[] = $availableMetrics[$name];
            } else {
                $this->metrics[] = $availableMetrics[$name];
            }
        }

        $this->defaultSortColumn = in_array($report['sort_metric'], $this->columns, true) ? $report['sort_metric'] : ($this->columns[0] ?? '');
        $this->defaultSortOrderDesc = $report['sort_order'] !== Model::SORT_ASC;
    }

    /**
     * All levels are loaded by this report, so core cannot find the dimension of a subtable
     * from the report that loads it.
     */
    public function getNthLevelTableDimension(int $level): ?Dimension
    {
        return $this->dimensions[$level] ?? null;
    }

    public function isEnabled()
    {
        return $this->report !== null && !empty($this->columns);
    }

    public function getDefaultTypeViewDataTable()
    {
        if ($this->report && $this->report['report_type'] === Model::TYPE_KEY_FIGURE) {
            return KeyFigure::ID;
        }

        return empty($this->dimensions) ? Evolution::ID : ($this->report['visualization'] ?? HtmlTable::ID);
    }

    public function configureView(ViewDataTable $view)
    {
        if ($this->report === null) {
            return;
        }

        $idMyReport = $this->report['idreport'];

        // Subtable, export and row evolution requests need to name the report again.
        $view->requestConfig->request_parameters_to_modify['idMyReport'] = $idMyReport;
        $view->requestConfig->request_parameters_to_modify['reportUniqueId'] = sprintf('RebelMyReports_getReportData_idMyReport--%d', $idMyReport);

        $idSite = Request::fromRequest()->getIntegerParameter('idSite', 0);
        $view->config->show_footer_message = (new ArchiveStatus())->getBuildingMessage($this->report, $idSite);
        $view->config->show_goals = false;
        $view->config->show_table_all_columns = false;
        $view->config->columns_to_display = array_merge(empty($this->dimensions) ? [] : ['label'], $this->columns);

        if ($this->dimensions) {
            $dimensions = $this->dimensions;
            $view->config->addTranslation('label', $dimensions[0]->getName());

            // The level of a subtable is only known once its data is loaded.
            $view->config->filters[] = function (DataTable $table) use ($view, $dimensions) {
                $level = (int) $table->getMetadata(API::LEVEL_METADATA);
                if (isset($dimensions[$level])) {
                    $view->config->addTranslation('label', $dimensions[$level]->getName());
                }

                // A subtable that fits on one page needs no paging of its own below its rows.
                $total = $table->getMetadata(DataTable::TOTAL_ROWS_BEFORE_LIMIT_METADATA_NAME) ?: $table->getRowsCount();
                $limit = (int) $view->requestConfig->filter_limit;
                if ($view->requestConfig->idSubtable && ($limit <= 0 || $total <= $limit)) {
                    $view->config->show_pagination_control = false;
                    $view->config->show_offset_information = false;
                    $view->config->show_limit_control = false;
                }
            };
        }
    }

    public function configureReportMetadata(&$availableReports, $infos)
    {
        if (!$this->isEnabled()) {
            return;
        }

        $idSites = $infos['idSites'] ?? [];
        if (!empty($infos['idSite']) || count($idSites) === 1) {
            $availableReports[] = $this->buildReportMetadata();
        }
    }
}
