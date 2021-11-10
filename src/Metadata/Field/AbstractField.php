<?php

namespace Antares\Crud\Metadata\Field;

use Antares\Crud\CrudException;
use Antares\Crud\Metadata\AbstractMetadata;
use Antares\Crud\Metadata\DataSource\DataSource;

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
            'letterCase' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
                'values' => ['upper', 'lower', 'sentence', 'capitalized'],
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
            'uicWidth' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'uicHeight' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'uicPattern' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'uicActionProperties' => [
                'type' => 'array',
                'required' => true,
                'nullable' => false,
                'default' => [],
            ],
            'dataSource' => [
                'type' => 'array|Antares\Crud\Metadata\DataSource\DataSource',
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
            'file',
            'image',
            'number',
            'password',
            'radio',
            'search',
            'select',
            'switch',
            'text',
            'textarea',
            'time',
            'timestamp',
            'timestamptz',
        ];
    }

    /**
     * @see AbstractMetadata::customValidations()
     *
     * @return void
     */
    protected function customValidations()
    {
        //--[ uicActionProperties ]--
        if (!is_array($this->uicActionProperties)) {
            throw CrudException::forInvalidObjectType('array', $this->uicActionProperties);
        } else {
            $items = [];
            foreach ($this->uicActionProperties as $item) {
                if (is_array($item)) {
                    $item = UicActionProperty::make($item);
                }
                if (!($item instanceof UicActionProperty)) {
                    throw CrudException::forInvalidObjectType(UicActionProperty::class, $item);
                }
                $items[] = $item;
            }
            $this->uicActionProperties = $items;
        }

        //--[ datasource ]--
        if (is_array($this->dataSource)) {
            $this->dataSource = DataSource::make($this->dataSource);
        }
    }
}
