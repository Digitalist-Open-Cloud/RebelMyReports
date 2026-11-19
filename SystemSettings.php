<?php

namespace Piwik\Plugins\RebelMyReports;

use Exception;
use Piwik\Piwik;
use Piwik\Settings\FieldConfig;

/**
 * The limits of the plugin. Core uses a value of the same name in the [RebelMyReports]
 * section of config.ini.php instead, and then hides the setting.
 */
class SystemSettings extends \Piwik\Settings\Plugin\SystemSettings
{
    public const TITLES = [
        Configuration::MAX_DIMENSIONS => 'RebelMyReports_SettingMaxDimensions',
        Configuration::MAX_ROWS => 'RebelMyReports_SettingMaxRows',
        Configuration::MAX_ROWS_SUBTABLE => 'RebelMyReports_SettingMaxRowsSubtable',
        Configuration::MAX_EXECUTION_TIME => 'RebelMyReports_SettingMaxExecutionTime',
        Configuration::PREVIEW_MAX_EXECUTION_TIME => 'RebelMyReports_SettingPreviewMaxExecutionTime',
    ];

    protected function init()
    {
        foreach (self::TITLES as $name => $title) {
            $highest = $name === Configuration::MAX_DIMENSIONS ? Configuration::HIGHEST_MAX_DIMENSIONS : PHP_INT_MAX;

            $this->makeSetting($name, Configuration::getDefault($name), FieldConfig::TYPE_INT, function (FieldConfig $field) use ($title, $highest) {
                $field->title = Piwik::translate($title);
                $field->inlineHelp = Piwik::translate($title . 'Help');
                $field->uiControl = FieldConfig::UI_CONTROL_TEXT;
                $field->validate = function ($value) use ($highest) {
                    if (!is_numeric($value) || $value < 1 || $value > $highest) {
                        throw new Exception(Piwik::translate('RebelMyReports_ErrorSettingRange', $highest === PHP_INT_MAX ? '' : $highest));
                    }
                };
            });
        }
    }
}
