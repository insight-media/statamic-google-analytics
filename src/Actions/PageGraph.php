<?php

namespace InsightMedia\StatamicGoogleAnalytics\Actions;

use InsightMedia\StatamicGoogleAnalytics\AnalyticsClient;
use InsightMedia\StatamicGoogleAnalytics\Period;
use Statamic\Entries\Entry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Statamic\Facades\Config;
use Statamic\Http\Controllers\Controller;

class PageGraph extends Controller
{

    public function getPageGraph(Request $request): array
    {
        $entry = Entry::find($request->entry);
        if (!$entry) {
            return [];
        }
        $url = $entry->url();

        $key = 'ga_stats_' . $url;

        $data = Cache::get($key, null);

        if ($data === null) {
            $period = Period::days(Config::get('statamic-google-analytics.analytics.days'), 30);

            $client = new AnalyticsClient;
            $data = $client->fetchTotalVisitorsAndPageViewsForURL($period, $url);

            Cache::put($key, $data, 600);
        }

        return [
            'labels' => $data->pluck('date'),
            'datasets' => [
                [
                    'label' => __('visitors'),
                    'data' => $data->pluck('visitors')
                ],
                [
                    'label' => __('views'),
                    'data' => $data->pluck('pageViews')
                ],
            ],

        ];

    }
}
