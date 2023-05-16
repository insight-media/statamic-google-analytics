<?php

namespace InsightMedia\StatamicGoogleAnalytics;

use Illuminate\Events\Dispatcher;
use Statamic\Events;
use Statamic\Facades\Config;

class Subscriber
{
    protected $blueprint;

    /**
     * List of subscribed events
     */
    protected array $events = [
        Events\EntryBlueprintFound::class => 'addFields',
    ];

    /**
     * Registers event listeners
     */
    public function subscribe(Dispatcher $events): void
    {
        foreach ($this->events as $event => $method) {
            $events->listen($event, self::class . '@' . $method);
        }
    }

    /**
     * Add SEOtamic fields to the collection blueprint
     */
    public function addFields(mixed $event)
    {
        $this->blueprint = $event->blueprint;

        if ($event->entry === null) {
            return;
        }

        $enabled = Config::get('statamic-google-analytics.analytics.page_graph', false);

        if (!$enabled) {
            return;
        }

        $fields = $this->getFields();

        collect($fields)->each(function ($field) {
            $this->blueprint->ensureFieldPrepended($field['handle'], $field['field'], 'Analytics');
        });
    }

    /**
     * Array of SEOtamic fields
     */
    private function getFields(): array
    {
        return [
            [
                'handle' => 'ga_page_stats_field',
                'field' => [
                    'display' => 'Analytics',
                    'listable' => 'hidden',
                    'type' => 'ga_page_stats_field',
                    'localizable' => false
                ],
            ]
        ];
    }
}
