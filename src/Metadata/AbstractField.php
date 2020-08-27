<?php

namespace Antares\Crud\Metadata;

abstract class AbstractField extends AbstractMetadata
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return [
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
                'required' => false,
                'nullable' => true,
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
            ],
            'mask' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'uic' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
                'values' => $this->uics(),
            ],
            'uicCols' => [
                'type' => 'integer',
                'required' => false,
                'nullable' => true,
            ],
            'uicPattern' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'dataSource' => [
                'type' => 'array|Antares\Crud\Metadata\DataSource',
                'required' => false,
                'nullable' => true,
            ],
            'disabled' => [
                'type' => 'boolean',
                'required' => false,
                'nullable' => true,
            ],
            'hidden' => [
                'type' => 'boolean',
                'required' => false,
                'nullable' => true,
            ],
            'default' => [
                'type' => 'mixed',
                'required' => false,
                'nullable' => true,
            ],
            'gridCols' => [
                'type' => 'integer',
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

    /**
     * @see AbstractMetadata::customValidates()
     *
     * @return void
     */
    protected function customValidates()
    {
        if (is_array($this->dataSource)) {
            $this->dataSource = DataSource::make($this->dataSource);
        }
    }
}