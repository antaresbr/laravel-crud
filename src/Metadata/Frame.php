<?php

namespace Antares\Crud\Metadata;

class Frame extends AbstractMetadata
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return [
            'title' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'size' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
                'values' => ['sm', 'md', 'lg', 'xl'],
            ],
            'backdrop' => [
                'type' => 'boolean|string',
                'required' => true,
                'nullable' => false,
                'values' => [true, false, 'static'],
                'default' => true,
            ],
        ];
    }
}
