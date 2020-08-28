<?php

namespace Antares\Crud\Metadata\Order;

use Antares\Crud\Metadata\AbstractMetadata;

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
