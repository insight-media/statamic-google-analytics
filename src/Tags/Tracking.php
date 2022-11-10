<?php

namespace InsightMedia\StatamicGoogleAnalytics\Tags;

use Statamic\Tags\Tags;
use Statamic\Facades\Config;
use Statamic\Facades\User;
use Statamic\View\View;

class Tracking extends Tags
{

    protected static $handle = 'ga';
    protected static $aliases = ['statamic-analytics'];

    public function index()
    {
        $tracking_id = str_replace(' ', '', $this->getConfig('tracking.tracking_id', ''));
        if (empty($tracking_id)) {
            return '<!-- Google Analytics Tracking code is not setup yet! -->';
        }

        $ignore_admins = $this->getConfig('tracking.ignore_admins', false);
        $user = User::current();
        if ($ignore_admins && $user && $user->can('cp:access')) {
            return;
        }

        $production_only = $this->getConfig('tracking.production_only', false);
        if ($production_only && Config::get('app.env') != "production") {
            return;
        }

        return View::make(
            'statamic-google-analytics::tracking-code'
        )->with([
            'tracking_id'             => $tracking_id,
            'additional_config_info'  => $this->getConfig('tracking.additional_config_info') ? json_encode($this->getConfig('tracking.additional_config_info')) : null,
        ])->render();
    }


    private function getConfig($key)
    {
        return Config::get('statamic-google-analytics.'.$key, null);
    }
}
