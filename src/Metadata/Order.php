<?php

namespace Antares\Crud\Metadata;

class Order extends AbstractMetadata
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return [
            'field' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
            ],
            'type' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
                'values' => [
                    'asc',
                    'desc',
                ],
                'default' => 'asc',
            ],
        ];
    }
}
