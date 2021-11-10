<?php

namespace Antares\Crud\Metadata\Field;

use Antares\Crud\Metadata\AbstractMetadata;

class UicActionProperty extends AbstractMetadata
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return [
            'action' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
                'values' => ['new', 'show', 'update', 'delete'],
            ],
            'property' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
                'values' => ['default', 'disabled', 'hidden'],
            ],
            'value' => [
                'type' => 'mixed',
                'required' => true,
                'nullable' => false,
            ],
        ];
    }
}
