<?php

namespace Antares\Crud\Metadata;

class Field extends AbstractMetadata
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return [
            'name' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
            ],
            'label' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'tooltip' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'placeholder' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'type' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
                'values' => $this->types(),
            ],
            'length' => [
                'type' => 'integer',
                'required' => false,
                'nullable' => true,
            ],
            'precision' => [
                'type' => 'integer',
                'required' => false,
                'nullable' => true,
            ],
            'unsigned' => [
                'type' => 'boolean',
                'required' => false,
                'nullable' => true,
                'default' => false,
            ],
            'uic' => [
                'type' => 'string',
                'required' => false,
                'nullable' => false,
                'values' => $this->uics(),
                'default' => 'text',
            ],
            'uicCols' => [
                'type' => 'integer',
                'required' => false,
                'nullable' => false,
                'default' => 1,
            ],
            'uicPattern' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'dataSource' => [
                'type' => 'Antares\Crud\Metadata\DataSource',
                'required' => false,
                'nullable' => true,
            ],
            'disabled' => [
                'type' => 'boolean',
                'required' => false,
                'nullable' => false,
                'default' => false,
            ],
            'hidden' => [
                'type' => 'boolean',
                'required' => false,
                'nullable' => false,
                'default' => false,
            ],
            'default' => [
                'type' => 'mixed',
                'required' => false,
                'nullable' => true,
            ],
        ];
    }

    /**
     * Get valid field types
     *
     * @return array
     */
    protected function types()
    {
        return [
            'bigint',
            'blob',
            'boolean',
            'date',
            'decimal',
            'integer',
            'longText',
            'smallint',
            'text',
            'time',
            'timestamp',
            'timestamptz',
        ];
    }

    /**
     * Get valid field uics
     *
     * @return array
     */
    protected function uics()
    {
        return [
            'checkbox',
            'date',
            'image',
            'number',
            'password',
            'radio',
            'search',
            'select',
            'text',
            'textarea',
            'time',
            'timestamp',
            'timestamptz',
        ];
    }
}
