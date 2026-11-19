<?php

namespace Piwik\Plugins\RebelMyReports;

/**
 * Example reports a user can create from the documentation page. The name and description of
 * an example are the translations RebelMyReports_Example<Id>Name and ...Description.
 */
class Examples
{
    public const DEFINITIONS = [
        'Countries' => [
            'report_type' => Model::TYPE_TABLE,
            'dimensions' => ['UserCountry.Country', 'DevicesDetection.BrowserName'],
            'metrics' => ['nb_visits', 'bounce_rate'],
            'row_limit' => 10,
        ],
        'EntryPages' => [
            'report_type' => Model::TYPE_TABLE,
            'dimensions' => ['Actions.EntryPageUrl'],
            'metrics' => ['nb_visits', 'bounce_rate'],
            'sort_metric' => 'bounce_rate',
            'metric_filters' => [
                ['metric' => 'nb_visits', 'operator' => 'gte', 'value' => 10],
                ['metric' => 'bounce_rate', 'operator' => 'gt', 'value' => 0.5],
            ],
        ],
        'Regions' => [
            'report_type' => Model::TYPE_TABLE,
            'dimensions' => ['UserCountry.Country'],
            'metrics' => ['nb_visits', 'nb_uniq_visitors'],
            // The group name is RebelMyReports_ExampleRegionsGroup.
            'grouped_values' => ['se', 'no', 'dk', 'fi', 'is'],
        ],
        'Returning' => [
            'report_type' => Model::TYPE_EVOLUTION,
            'metrics' => ['nb_visits', 'pageviews'],
            'segment_filter' => 'visitorType==returning',
        ],
        'Visits' => [
            'report_type' => Model::TYPE_KEY_FIGURE,
            'metrics' => ['nb_visits'],
        ],
        'Channels' => [
            'report_type' => Model::TYPE_TABLE,
            'dimensions' => ['Referrers.ReferrerType'],
            'metrics' => ['nb_visits', 'pageviews'],
            'visualization' => 'graphVerticalBar',
            // Added to the metrics of the report. Its name is RebelMyReports_ExampleChannelsMetric.
            'calculated_metric' => 'pageviews / nb_visits',
        ],
    ];
}
