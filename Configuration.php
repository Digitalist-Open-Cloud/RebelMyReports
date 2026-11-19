<?php

namespace Piwik\Plugins\RebelMyReports;

use Piwik\Config;
use Piwik\Container\StaticContainer;

/**
 * The limits of the plugin: system settings that the [RebelMyReports] section of
 * config.ini.php overrides.
 */
class Configuration
{
    public const MAX_DIMENSIONS = 'max_dimensions';
    public const MAX_ROWS = 'datatable_archiving_maximum_rows';
    public const MAX_ROWS_SUBTABLE = 'datatable_archiving_maximum_rows_subtable';
    public const MAX_EXECUTION_TIME = 'max_execution_time';
    public const PREVIEW_MAX_EXECUTION_TIME = 'preview_max_execution_time';

    // The queries name the dimension columns d0 to d9.
    public const HIGHEST_MAX_DIMENSIONS = 9;

    public static function getDefault(string $name): int
    {
        switch ($name) {
            case self::MAX_DIMENSIONS:
                return 3;
            case self::MAX_ROWS:
                return (int) Config::getInstance()->General['datatable_archiving_maximum_rows_standard'];
            case self::MAX_ROWS_SUBTABLE:
                return 100;
            case self::MAX_EXECUTION_TIME:
                return 600;
            default:
                return 30;
        }
    }

    public function getMaxDimensions(): int
    {
        return min(self::HIGHEST_MAX_DIMENSIONS, $this->get(self::MAX_DIMENSIONS));
    }

    /**
     * How many rows a report keeps when it is archived. The rest is summed into one row.
     */
    public function getMaxRows(): int
    {
        return $this->get(self::MAX_ROWS);
    }

    public function getMaxRowsInSubtable(): int
    {
        return $this->get(self::MAX_ROWS_SUBTABLE);
    }

    /**
     * How many seconds a query that archives a report may run.
     */
    public function getMaxExecutionTime(): int
    {
        return $this->get(self::MAX_EXECUTION_TIME);
    }

    /**
     * How many seconds a query of the preview in the report editor may run.
     */
    public function getPreviewMaxExecutionTime(): int
    {
        return $this->get(self::PREVIEW_MAX_EXECUTION_TIME);
    }

    private function get(string $name): int
    {
        $value = (int) StaticContainer::get(SystemSettings::class)->getSetting($name)->getValue();

        return $value > 0 ? $value : self::getDefault($name);
    }
}
