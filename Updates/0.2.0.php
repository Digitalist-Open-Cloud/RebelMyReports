<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Plugins\RebelMyReports\Metrics\MetricModel;
use Piwik\Updater;
use Piwik\Updates as PiwikUpdates;

/**
 * Adds the table of the calculated metrics.
 */
class Updates_0_2_0 extends PiwikUpdates
{
    public function doUpdate(Updater $updater)
    {
        (new MetricModel())->install();
    }
}
