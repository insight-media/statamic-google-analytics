<?php

namespace InsightMedia\StatamicGoogleAnalytics\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use InsightMedia\StatamicGoogleAnalytics\AnalyticsClient;
use InsightMedia\StatamicGoogleAnalytics\FieldType\GaPageStatsFieldType;
use InsightMedia\StatamicGoogleAnalytics\Period;
use InsightMedia\StatamicGoogleAnalytics\ServiceProvider;
use InsightMedia\StatamicGoogleAnalytics\Tags\Tracking;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use Statamic\Events\EntryBlueprintFound;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Entry;
use Statamic\Facades\User;
use Statamic\Fields\Field;
use Statamic\Testing\AddonTestCase;
use Statamic\Widgets\Loader;

class Statamic6CompatibilityTest extends AddonTestCase
{
    protected string $addonServiceProvider = ServiceProvider::class;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('cache.default', 'array');
        $app['config']->set('app.key', 'base64:'.base64_encode(str_repeat('a', 32)));
        $app['config']->set('statamic.routes.action', 'analytics-actions');
    }

    #[DataProvider('widgets')]
    public function test_widgets_render_tables_and_self_contained_charts(string $group, string $display): void
    {
        $rows = collect([[
            'date' => Carbon::parse('2026-10-01'),
            'pageTitle' => 'A "quoted" <page> & title',
            'url' => '/example',
            'visitors' => 10,
            'pageViews' => 20,
            'browser' => 'Firefox',
            'country' => 'Belgium',
            'sessions' => 15,
        ]]);

        $client = Mockery::mock(AnalyticsClient::class);
        $client->shouldReceive('fetch'.ucfirst($group))->once()->withArgs(function (Period $period, ...$args) {
            return (int) $period->startDate->diffInDays($period->endDate) === 7;
        })->andReturn($rows);
        $this->app->instance(AnalyticsClient::class, $client);

        $widget = app(Loader::class)->load('analytics', ['group' => $group, 'display' => $display, 'days' => 7]);
        $html = $widget->html()->render();

        $this->assertStringNotContainsString('<script', $html);
        $this->assertStringNotContainsString('statamic-google-analytics::messages', $html);

        if ($display === 'table') {
            $this->assertStringContainsString('<table', $html);
        } else {
            $this->assertStringContainsString('<'.$display.'-chart', $html);
            $this->assertStringContainsString(':chartdata="', $html);
            $this->assertStringContainsString('JSON.parse(', $html);
            $this->assertStringNotContainsString('<page>', $html);
        }
    }

    public static function widgets(): iterable
    {
        foreach (['totalVisitorsAndPageViews', 'mostVisitedPages', 'topReferrers', 'topBrowsers', 'topCountries'] as $group) {
            foreach (['table', 'bar', 'line', 'pie', 'doughnut'] as $display) {
                yield $group.' '.$display => [$group, $display];
            }
        }
    }

    public function test_vite_assets_are_registered_for_publishing(): void
    {
        $paths = \Illuminate\Support\ServiceProvider::pathsToPublish(ServiceProvider::class, 'statamic-google-analytics');

        $this->assertCount(1, $paths);
        $source = array_key_first($paths);
        $this->assertSame(public_path('vendor/statamic-google-analytics/build/'), $paths[$source]);

        $manifest = json_decode(file_get_contents($source.'manifest.json'), true);
        $this->assertFileExists($source.$manifest['resources/js/cp.js']['file']);
    }

    public function test_entry_field_uses_its_own_value_and_the_configured_action_url(): void
    {
        $entry = Entry::make()->id('example-entry');
        $field = (new Field('ga_page_stats_field', []))->setParent($entry);
        $fieldtype = (new GaPageStatsFieldType)->setField($field);
        $value = $fieldtype->preProcess(null);

        $this->assertSame('example-entry', $value['entry']);
        $this->assertSame(url('/analytics-actions/statamic-google-analytics/page-data'), $value['url']);
        $this->assertNull($fieldtype->process($value));
    }

    public function test_entry_chart_returns_date_labels_and_cached_metrics(): void
    {
        $entry = Mockery::mock(\Statamic\Contracts\Entries\Entry::class);
        $entry->shouldReceive('url')->andReturn('/example');
        Entry::shouldReceive('find')->twice()->with('example-entry')->andReturn($entry);

        $client = Mockery::mock(AnalyticsClient::class);
        $client->shouldReceive('fetchTotalVisitorsAndPageViewsForURL')->once()->withArgs(function (Period $period, string $url) {
            return $url === '/example' && (int) $period->startDate->diffInDays($period->endDate) === 30;
        })->andReturn(collect([[
            'date' => Carbon::parse('2026-10-01'),
            'visitors' => 10,
            'pageViews' => 20,
        ]]));
        $this->app->instance(AnalyticsClient::class, $client);

        $url = route('statamic.statamic-google-analytics.page-data', ['entry' => 'example-entry']);

        for ($i = 0; $i < 2; $i++) {
            $this->getJson($url)->assertOk()->assertJsonPath('labels.0', '2026-10-01')
                ->assertJsonPath('datasets.0.data.0', 10)
                ->assertJsonPath('datasets.1.data.0', 20);
        }

        $this->assertNotNull(Cache::get('ga_stats_/example'));
    }

    public function test_subscriber_adds_the_analytics_field_to_entry_blueprints(): void
    {
        $blueprint = Blueprint::make()->setContents(['tabs' => []]);
        event(new EntryBlueprintFound($blueprint, Entry::make()->id('example-entry')));

        $this->assertSame('ga_page_stats_field', $blueprint->field('ga_page_stats_field')->type());
    }

    public function test_tracking_excludes_users_with_control_panel_access(): void
    {
        config(['statamic-google-analytics.tracking.tracking_id' => 'G-EXAMPLE']);
        $user = Mockery::mock(\Statamic\Contracts\Auth\User::class);
        $user->shouldReceive('can')->once()->with('access cp')->andReturnTrue();
        User::shouldReceive('current')->once()->andReturn($user);

        $this->assertNull((new Tracking)->index());
    }

    public function test_tracking_renders_for_visitors(): void
    {
        config([
            'statamic-google-analytics.tracking.tracking_id' => 'G-EXAMPLE',
            'statamic-google-analytics.tracking.production_only' => false,
        ]);
        User::shouldReceive('current')->andReturnNull();

        $this->assertStringContainsString("gtag('config', 'G-EXAMPLE'", (new Tracking)->index());
    }
}
