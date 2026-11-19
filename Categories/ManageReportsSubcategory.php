<?php

namespace Piwik\Plugins\RebelMyReports\Categories;

use Piwik\Category\Subcategory;
use Piwik\Plugins\RebelMyReports\GetReportData;

class ManageReportsSubcategory extends Subcategory
{
    public const ID = 'RebelMyReports_ManageReports';

    protected $categoryId = GetReportData::DEFAULT_CATEGORY;
    protected $id = self::ID;
    protected $order = 9999;
}
