<?php

namespace Statamic\Fieldtypes;

use Statamic\Fields\Fieldtype;

class Revealer extends Fieldtype
{
    protected $categories = ['controls', 'special'];
    protected $localizable = false;
    protected $validatable = false;
    protected $defaultable = false;

    protected function configFieldItems(): array
    {
        return [
            'mode' => [
                'display' => __('Mode'),
                'instructions' => __('statamic::fieldtypes.revealer.config.mode'),
                'type' => 'select',
                'options' => [
                    'button' => __('Button'),
                    'toggle' => __('Toggle'),
                ],
                'default' => 'button',
            ],
        ];
    }

    public function preProcess($data)
    {
        return $data ?: false;
    }

    public function process($data)
    {
        // Don't set as null because it can
        // masquerade as lost data.

        // return null;
    }
}
