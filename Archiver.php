<?php

namespace Piwik\Plugins\RebelMyReports;

// Needed for Matomo to archive this plugin: its record builders are registered by event.
class Archiver extends \Piwik\Plugin\Archiver
{
    public static function getRecordName(array $report): string
    {
        return 'RebelMyReports_' . (int) $report['idreport'] . '_' . (int) $report['revision'];
    }
}
