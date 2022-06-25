<?php

namespace InsightMedia\StatamicGoogleAnalytics\Widgets;

use Google\Service\Exception as GoogleException;
use Exception;
use Illuminate\Support\Str;
use Illuminate\View\View;
use InsightMedia\StatamicGoogleAnalytics\AnalyticsClient;
use InsightMedia\StatamicGoogleAnalytics\Period;
use Statamic\Facades\Config;
use Statamic\Widgets\Widget;
use Statamic\Exceptions\MethodNotFoundException;

/**
 * Widgets collection stored in here
 */
class Analytics extends Widget
{

    protected array $views = [
        'totalVisitorsAndPageViews',
        'mostVisitedPages',
        'topReferrers',
        'topBrowsers',
        'topCountries',
    ];

    protected AnalyticsClient $client;

    public function __construct()
    {
        $this->client = new AnalyticsClient;
    }

    /**
     * The HTML that should be shown in the widget.
     */
    public function html(): View
    {

        $version = Str::camel($this->config('group', 'most_visited_pages'));

        if (! in_array($version, $this->views)) {
            throw new MethodNotFoundException(__('statamic-google-analytics::messages.method_not_found', ['widget' => $version]));
        }

        return $this->{$version}();
    }


    protected function totalVisitorsAndPageViews(): View
    {
        $data = $message = null;
        $period = Period::days(Config::get('statamic-google-analytics.analytics.days'), 30);

        try {
            $data = $this->client->fetchTotalVisitorsAndPageViews($period);
        } catch (GoogleException $e) {
            $message = $this->decodeGoogleException($e);
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return view('insight-media-analytics::widgets.total-visitors-and-page', ['data' => $data, 'message' => $message,  'config' => $this->config()]);
    }

    protected function topReferrers(): View
    {
        $data = $message = null;
        $period = Period::days(Config::get('statamic-google-analytics.analytics.days'), 30);
        $maxResults = $this->config('max_results', 10);

        try {
            $data = $this->client->fetchTopReferrers($period, $maxResults);
        } catch (GoogleException $e) {
            $message = $this->decodeGoogleException($e);
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return view('insight-media-analytics::widgets.top-referrers', ['data' => $data, 'message' => $message,  'config' => $this->config()]);
    }

    protected function topBrowsers(): View
    {
        $data = $message = null;
        $period = Period::days(Config::get('statamic-google-analytics.analytics.days'), 30);
        $maxResults = $this->config('max_results', 10);

        try {
            $data = $this->client->fetchTopBrowsers($period, $maxResults);
        } catch (GoogleException $e) {
            $message = $this->decodeGoogleException($e);
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return view('insight-media-analytics::widgets.top-browsers', ['data' => $data, 'message' => $message,     'config' => $this->config()]);
    }

    protected function mostVisitedPages(): View
    {

        $data = $message = null;
        $period = Period::days(Config::get('statamic-google-analytics.analytics.days'), 30);
        $maxResults = $this->config('max_results', 10);

        try {
            $data = $this->client->fetchMostVisitedPages($period, $maxResults);
        } catch (GoogleException $e) {
            $message = $this->decodeGoogleException($e);
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return view('insight-media-analytics::widgets.most-visited-pages', ['data' => $data, 'message' => $message,  'config' => $this->config()]);
    }

    protected function topCountries(): View
    {
        $data = $message = null;
        $period = Period::days(Config::get('statamic-google-analytics.analytics.days'), 30);
        $maxResults = $this->config('max_results', 10);

        try {
            $data = $this->client->fetchTopCountries($period, $maxResults);
        } catch (GoogleException $e) {
            $message = $this->decodeGoogleException($e);
        } catch (Exception $e) {
            $message = $e->getMessage();
        }

        return view('insight-media-analytics::widgets.top-countries', [
            'data'    => $data,
            'message' => $message,
            'config'  => $this->config()
        ]);
    }

    private function decodeGoogleException(GoogleException $e): string
    {
        return current($e->getErrors())['message'];
    }
}
