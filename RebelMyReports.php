<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Category\Subcategory;
use Piwik\Common;
use Piwik\Container\StaticContainer;
use Piwik\Plugin;
use Piwik\Plugins\RebelMyReports\Archiving\QueryBuilder;
use Piwik\Plugins\RebelMyReports\Archiving\ReportTableBuilder;
use Piwik\Plugins\RebelMyReports\Metrics\MetricModel;
use Piwik\Plugins\RebelMyReports\RecordBuilders\ReportRecord;
use Piwik\Request;

class RebelMyReports extends Plugin
{
    public function registerEvents()
    {
        return [
            'Db.getTablesInstalled' => 'getTablesInstalled',
            'Archiver.addRecordBuilders' => 'addRecordBuilders',
            'Report.addReports' => 'addReports',
            'Category.addSubcategories' => 'addSubcategories',
            'SitesManager.deleteSite.end' => 'deleteReportsOfSite',
            'AssetManager.getStylesheetFiles' => 'getStylesheetFiles',
            'Translate.getClientSideTranslationKeys' => 'getClientSideTranslationKeys',
        ];
    }

    public function install()
    {
        (new Model())->install();
        (new MetricModel())->install();
    }

    public function uninstall()
    {
        (new Model())->uninstall();
        (new MetricModel())->uninstall();
    }

    public function getTablesInstalled(&$allTablesInstalled)
    {
        $allTablesInstalled[] = Common::prefixTable(Model::TABLE);
        $allTablesInstalled[] = Common::prefixTable(MetricModel::TABLE);
    }

    public function addRecordBuilders(&$recordBuilders)
    {
        $queryBuilder = StaticContainer::get(QueryBuilder::class);
        $tableBuilder = StaticContainer::get(ReportTableBuilder::class);

        foreach ((new Model())->getAllReports() as $report) {
            $recordBuilders[] = new ReportRecord($report, $queryBuilder, $tableBuilder);
        }
    }

    public function addReports(&$instances)
    {
        $reports = $this->getReportsOfRequestedSite();

        foreach ($reports as $report) {
            $instance = new GetReportData();
            $instance->initFromReport($report);
            $instances[] = $instance;
        }

        if (empty($reports)) {
            // Keeps the API method mapped to the report class, which core caches per site.
            $instances[] = new GetReportData();
        }
    }

    public function addSubcategories(&$subcategories)
    {
        $reports = array_column($this->getReportsOfRequestedSite(), null, 'idreport');
        $added = [];

        foreach ($reports as $report) {
            $id = GetReportData::getSubcategoryIdOf($report);
            if (isset($added[$id]) || strpos($id, GetReportData::OWN_PAGE_PREFIX) !== 0) {
                // Shown on a page that exists without this plugin, or the page was added already.
                continue;
            }
            $added[$id] = true;

            // The page is named after the report it was created for, while that report exists.
            $idOwner = (int) substr($id, strlen(GetReportData::OWN_PAGE_PREFIX));
            $owner = $reports[$idOwner] ?? $report;

            $subcategory = new Subcategory();
            $subcategory->setId($id);
            $subcategory->setName($owner['name']);
            $subcategory->setCategoryId(GetReportData::getCategoryIdOf($report));
            $subcategory->setOrder(100 + $owner['position']);
            $subcategories[] = $subcategory;
        }
    }

    public function deleteReportsOfSite($idSite)
    {
        StaticContainer::get(ReportManager::class)->deleteReportsOfSite((int) $idSite);
        (new MetricModel())->deleteMetricsOfSite((int) $idSite);
    }

    public function getStylesheetFiles(&$stylesheets)
    {
        $stylesheets[] = 'plugins/RebelMyReports/stylesheets/menuIcon.less';
        $stylesheets[] = 'plugins/RebelMyReports/vue/src/InvalidateMyReports/InvalidateMyReports.less';
        $stylesheets[] = 'plugins/RebelMyReports/vue/src/MyDuplicateForm/MyDuplicateForm.less';
        $stylesheets[] = 'plugins/RebelMyReports/vue/src/MyMetricsManager/MyMetricsManager.less';
        $stylesheets[] = 'plugins/RebelMyReports/vue/src/MyReportEditor/MyReportEditor.less';
        $stylesheets[] = 'plugins/RebelMyReports/vue/src/MyReportExamples/MyReportExamples.less';
        $stylesheets[] = 'plugins/RebelMyReports/vue/src/MyReportExport/MyReportExport.less';
        $stylesheets[] = 'plugins/RebelMyReports/vue/src/MyReportItemPicker/MyReportItemPicker.less';
        $stylesheets[] = 'plugins/RebelMyReports/vue/src/MyReportKeyFigure/MyReportKeyFigure.less';
        $stylesheets[] = 'plugins/RebelMyReports/vue/src/MyReportPreview/MyReportPreview.less';
        $stylesheets[] = 'plugins/RebelMyReports/vue/src/MyReportsList/MyReportsList.less';
    }

    public function getClientSideTranslationKeys(&$translationKeys)
    {
        $translationKeys[] = 'CoreHome_ThereIsNoDataForThisReport';
        $translationKeys[] = 'General_Actions';
        $translationKeys[] = 'General_Cancel';
        $translationKeys[] = 'General_Delete';
        $translationKeys[] = 'General_Description';
        $translationKeys[] = 'General_Edit';
        $translationKeys[] = 'General_Export';
        $translationKeys[] = 'General_ExportThisReport';
        $translationKeys[] = 'General_LoadingData';
        $translationKeys[] = 'General_Metrics';
        $translationKeys[] = 'General_Name';
        $translationKeys[] = 'General_No';
        $translationKeys[] = 'General_Remove';
        $translationKeys[] = 'General_Save';
        $translationKeys[] = 'General_Value';
        $translationKeys[] = 'General_Yes';
        $translationKeys[] = 'RebelMyReports_AddDimension';
        $translationKeys[] = 'RebelMyReports_AddMetric';
        $translationKeys[] = 'RebelMyReports_AddFilter';
        $translationKeys[] = 'RebelMyReports_AllWebsites';
        $translationKeys[] = 'RebelMyReports_ArchivedData';
        $translationKeys[] = 'RebelMyReports_AddValueGroup';
        $translationKeys[] = 'RebelMyReports_AvailableFor';
        $translationKeys[] = 'RebelMyReports_AvailableForAllWebsites';
        $translationKeys[] = 'RebelMyReports_AvailableForAllWebsitesHelp';
        $translationKeys[] = 'RebelMyReports_CalculatedMetrics';
        $translationKeys[] = 'RebelMyReports_CalculatedMetricsIntro';
        $translationKeys[] = 'RebelMyReports_CreateCalculatedMetric';
        $translationKeys[] = 'RebelMyReports_CreateNewReport';
        $translationKeys[] = 'RebelMyReports_Created';
        $translationKeys[] = 'RebelMyReports_DeleteMetricConfirm';
        $translationKeys[] = 'RebelMyReports_DeleteReportConfirm';
        $translationKeys[] = 'RebelMyReports_Dimensions';
        $translationKeys[] = 'RebelMyReports_Documentation';
        $translationKeys[] = 'RebelMyReports_Duplicate';
        $translationKeys[] = 'RebelMyReports_DuplicateTo';
        $translationKeys[] = 'RebelMyReports_DuplicatedTo';
        $translationKeys[] = 'RebelMyReports_EditReport';
        $translationKeys[] = 'RebelMyReports_ExampleCreate';
        $translationKeys[] = 'RebelMyReports_ExampleCreated';
        $translationKeys[] = 'RebelMyReports_ExamplesIntro';
        $translationKeys[] = 'RebelMyReports_ExamplesTitle';
        $translationKeys[] = 'RebelMyReports_FilterOperator';
        $translationKeys[] = 'RebelMyReports_FirstMetric';
        $translationKeys[] = 'RebelMyReports_Format';
        $translationKeys[] = 'RebelMyReports_FormatDuration';
        $translationKeys[] = 'RebelMyReports_FormatMoney';
        $translationKeys[] = 'RebelMyReports_FormatNumber';
        $translationKeys[] = 'RebelMyReports_FormatPercent';
        $translationKeys[] = 'RebelMyReports_Formula';
        $translationKeys[] = 'RebelMyReports_FormulaHelp';
        $translationKeys[] = 'RebelMyReports_InsertMetric';
        $translationKeys[] = 'RebelMyReports_KeyFigureHelp';
        $translationKeys[] = 'General_Date';
        $translationKeys[] = 'General_DateRangeFrom';
        $translationKeys[] = 'General_DateRangeInPeriodList';
        $translationKeys[] = 'General_DateRangeTo';
        $translationKeys[] = 'General_Reports';
        $translationKeys[] = 'Intl_PeriodMonth';
        $translationKeys[] = 'Intl_PeriodYear';
        $translationKeys[] = 'General_Segment';
        $translationKeys[] = 'RebelMyReports_InvalidateAllReports';
        $translationKeys[] = 'RebelMyReports_InvalidateAllSegments';
        $translationKeys[] = 'RebelMyReports_InvalidateButton';
        $translationKeys[] = 'RebelMyReports_InvalidateDone';
        $translationKeys[] = 'RebelMyReports_InvalidateIntro';
        $translationKeys[] = 'RebelMyReports_InvalidatePeriodHelp';
        $translationKeys[] = 'RebelMyReports_InvalidateReports';
        $translationKeys[] = 'RebelMyReports_ManageReports';
        $translationKeys[] = 'RebelMyReports_ManageReportsIntro';
        $translationKeys[] = 'RebelMyReports_MaxDimensionsReached';
        $translationKeys[] = 'RebelMyReports_MaxMetricsReached';
        $translationKeys[] = 'RebelMyReports_MoveEarlier';
        $translationKeys[] = 'RebelMyReports_MoveLater';
        $translationKeys[] = 'RebelMyReports_MyReports';
        $translationKeys[] = 'RebelMyReports_NewPageForReport';
        $translationKeys[] = 'RebelMyReports_NoArchivedData';
        $translationKeys[] = 'RebelMyReports_NoCalculatedMetricsYet';
        $translationKeys[] = 'RebelMyReports_NoReportsYet';
        $translationKeys[] = 'RebelMyReports_OnlyRowsWhere';
        $translationKeys[] = 'RebelMyReports_OperatorAtLeast';
        $translationKeys[] = 'RebelMyReports_OperatorAtMost';
        $translationKeys[] = 'RebelMyReports_OperatorEquals';
        $translationKeys[] = 'RebelMyReports_OperatorGreaterThan';
        $translationKeys[] = 'RebelMyReports_OperatorLessThan';
        $translationKeys[] = 'RebelMyReports_Pause';
        $translationKeys[] = 'RebelMyReports_Paused';
        $translationKeys[] = 'RebelMyReports_Preview';
        $translationKeys[] = 'RebelMyReports_PreviewHint';
        $translationKeys[] = 'RebelMyReports_PreviewNeedsMetric';
        $translationKeys[] = 'RebelMyReports_PreviewSlow';
        $translationKeys[] = 'RebelMyReports_ProblemFailed';
        $translationKeys[] = 'RebelMyReports_ProblemTimeout';
        $translationKeys[] = 'RebelMyReports_ProblemTruncated';
        $translationKeys[] = 'RebelMyReports_ReportPage';
        $translationKeys[] = 'RebelMyReports_ReportPageHelp';
        $translationKeys[] = 'RebelMyReports_ReportSaved';
        $translationKeys[] = 'RebelMyReports_ReportType';
        $translationKeys[] = 'RebelMyReports_ReportingCategory';
        $translationKeys[] = 'RebelMyReports_Reorder';
        $translationKeys[] = 'RebelMyReports_Resume';
        $translationKeys[] = 'RebelMyReports_RowLimit';
        $translationKeys[] = 'RebelMyReports_RowLimitHelp';
        $translationKeys[] = 'RebelMyReports_Rows';
        $translationKeys[] = 'RebelMyReports_RowsHelp';
        $translationKeys[] = 'RebelMyReports_SegmentFilter';
        $translationKeys[] = 'RebelMyReports_SegmentFilterHelp';
        $translationKeys[] = 'RebelMyReports_SortAscending';
        $translationKeys[] = 'RebelMyReports_SortBy';
        $translationKeys[] = 'RebelMyReports_SortDescending';
        $translationKeys[] = 'RebelMyReports_SortOrder';
        $translationKeys[] = 'RebelMyReports_ThisWebsite';
        $translationKeys[] = 'RebelMyReports_TypeEvolution';
        $translationKeys[] = 'RebelMyReports_TypeKeyFigure';
        $translationKeys[] = 'RebelMyReports_TypeTable';
        $translationKeys[] = 'RebelMyReports_UpdateWarning';
        $translationKeys[] = 'RebelMyReports_Updated';
        $translationKeys[] = 'RebelMyReports_ValueGroupAddValue';
        $translationKeys[] = 'RebelMyReports_ValueGroupContains';
        $translationKeys[] = 'RebelMyReports_ValueGroupDimension';
        $translationKeys[] = 'RebelMyReports_ValueGroupEnds';
        $translationKeys[] = 'RebelMyReports_ValueGroupEquals';
        $translationKeys[] = 'RebelMyReports_ValueGroupName';
        $translationKeys[] = 'RebelMyReports_ValueGroupOperator';
        $translationKeys[] = 'RebelMyReports_ValueGroupStarts';
        $translationKeys[] = 'RebelMyReports_ValueGroups';
        $translationKeys[] = 'RebelMyReports_ValueGroupsHelp';
        $translationKeys[] = 'General_Piechart';
        $translationKeys[] = 'General_Table';
        $translationKeys[] = 'General_TagCloud';
        $translationKeys[] = 'General_VBarGraph';
        $translationKeys[] = 'RebelMyReports_ViewReport';
        $translationKeys[] = 'RebelMyReports_Visualization';
        $translationKeys[] = 'RebelMyReports_VisualizationHelp';
        $translationKeys[] = 'RebelMyReports_WaitingToBeArchived';
        $translationKeys[] = 'RebelMyReports_WhereToShow';
    }

    private function getReportsOfRequestedSite(): array
    {
        $idSite = Request::fromRequest()->getIntegerParameter('idSite', 0);

        return $idSite > 0 ? (new Model())->getReports($idSite) : [];
    }
}
