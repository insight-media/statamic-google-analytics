<?php

namespace InsightMedia\StatamicGoogleAnalytics\FieldType;

use Statamic\Entries\Entry;
use Statamic\Fields\Fieldtype;

use function PHPUnit\Framework\isInstanceOf;

class GaPageStatsFieldType extends Fieldtype
{
    protected $selectable = false;
    protected static $title = 'Google Analytics Statistiek';
    protected static $handle = 'ga_page_stats_field';

  /**
   * The blank/default value
   */
    public function blank(): ?array
    {
        return null;
    }

  /**
   * Pre-process the data before it gets sent to the publish page
   */
    public function preProcess(mixed $data): array
    {
        return [
            'title' => "testing",
            'entry' => ($this->field()->parent() instanceof Entry) ? $this->field()->parent()->id() : ''
        ];
    }

  /**
   * Process the data before it gets saved
   */
    public function process(mixed $data): mixed
    {
        return null;
    }
}
