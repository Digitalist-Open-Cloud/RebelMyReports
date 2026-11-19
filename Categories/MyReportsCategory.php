<?php

namespace Piwik\Plugins\RebelMyReports\Categories;

use Piwik\Category\Category;
use Piwik\Plugins\RebelMyReports\GetReportData;

class MyReportsCategory extends Category
{
    protected $id = GetReportData::DEFAULT_CATEGORY;
    protected $order = 65;
    protected $icon = 'rebelMyReportsIcon';
}
