<?php

namespace Antares\Crud\Metadata\Layout;

use Antares\Crud\Metadata\AbstractMetadata;

abstract class AbstractLayout extends AbstractMetadata
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return [
            'type' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
            ],
            'name' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'cols' => [
                'type' => 'integer',
                'required' => false,
                'nullable' => true,
            ],
            'width' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'height' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
        ];
    }
}
