<?php

namespace Piwik\Plugins\RebelMyReports\Widgets;

use Piwik\Config;
use Piwik\Piwik;
use Piwik\Plugins\RebelMyReports\Categories\ManageReportsSubcategory;
use Piwik\Plugins\RebelMyReports\Configuration;
use Piwik\Plugins\RebelMyReports\GetReportData;
use Piwik\Request;
use Piwik\Widget\Widget;
use Piwik\Widget\WidgetConfig;

/**
 * The page to create, change and delete reports.
 */
class ManageReports extends Widget
{
    public static function configure(WidgetConfig $config)
    {
        $idSite = Request::fromRequest()->getIntegerParameter('idSite', 0);

        $config->setCategoryId(GetReportData::DEFAULT_CATEGORY);
        $config->setSubcategoryId(ManageReportsSubcategory::ID);
        $config->setName(ManageReportsSubcategory::ID);
        $config->setIsNotWidgetizable();
        $config->setIsEnabled($idSite > 0 && Piwik::isUserHasWriteAccess($idSite));
    }

    public function render()
    {
        Piwik::checkUserHasWriteAccess(Request::fromRequest()->getIntegerParameter('idSite'));

        $months = (int) str_replace('last', '', (string) Config::getInstance()->General['rearchive_reports_in_past_last_n_months']);

        return sprintf(
            '<div vue-entry="RebelMyReports.ManageMyReports" rearchive-months="%d" max-dimensions="%d"></div>',
            $months,
            (new Configuration())->getMaxDimensions()
        );
    }
}
