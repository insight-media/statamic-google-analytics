<?php

namespace InsightMedia\StatamicGoogleAnalytics;

use Statamic\Providers\AddonServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use InsightMedia\StatamicGoogleAnalytics\Actions\PageGraph;
use InsightMedia\StatamicGoogleAnalytics\Subscriber;
use InsightMedia\StatamicGoogleAnalytics\Tags\Tracking;
use InsightMedia\StatamicGoogleAnalytics\Widgets\Analytics;
use InsightMedia\StatamicGoogleAnalytics\FieldType\GaPageStatsFieldType;

class ServiceProvider extends AddonServiceProvider
{

    protected $viewNamespace = 'insight-media-analytics';

    protected $scripts = [
        __DIR__ . '/../resources/dist/js/cp.js'
    ];

    protected $widgets = [
        Analytics::class
    ];

    protected $tags = [
        Tracking::class
    ];

    protected $fieldtypes = [
        GaPageStatsFieldType::class
    ];


    public function bootAddon()
    {

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'statamic-google-analytics');
        $this->publishes([
            __DIR__ . '/../config/' => config_path(),
        ], 'statamic-google-analytics-config');

        Event::subscribe(Subscriber::class);

        $this->registerActionRoutes(function () {
            Route::get('page-data/', [PageGraph::class, 'getPageGraph']);
        });
    }
}
