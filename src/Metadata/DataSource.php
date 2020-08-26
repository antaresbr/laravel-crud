<?php

namespace Antares\Crud\Metadata;

use Antares\Crud\CrudException;

class DataSource extends AbstractMetadata
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
                'values' => [
                    'picklist',
                    'table',
                ],
            ],
            'id' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'sourceKey' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
            ],
            'api' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'showFields' => [
                'type' => 'string|array',
                'required' => false,
                'nullable' => true,
            ],
            'optionFields' => [
                'type' => 'string|array',
                'required' => false,
                'nullable' => true,
            ],
        ];
    }

    /**
     * @see AbstractMetadata::customDefaults()
     *
     * @return void
     */
    protected function customDefaults(array &$data)
    {
        if (!empty($data['type']) and $data['type'] == 'picklist') {
            if (empty($data['sourceKey'])) {
                $data['sourceKey'] = 'key';
            }
            if (empty($data['showFields'])) {
                $data['showFields'] = ['label'];
            }
            if (empty($data['optionFields'])) {
                $data['optionFields'] = ['label'];
            }
        }

        if (!empty($data['type']) and $data['type'] == 'table') {
            if (empty($data['sourceKey'])) {
                $data['sourceKey'] = 'id';
            }
            if (empty($data['api']) and !empty($data['id'])) {
                $data['api'] = $data['id'];
            }
        }
    }

    /**
     * @see AbstractMetadata::customValidates()
     *
     * @return void
     */
    protected function customValidates()
    {
        //--[ showFields ]--

        if (!empty($this->showFields) and is_string($this->showFields)) {
            $fields = [];
            foreach (explode('|', $this->showFields) as $field) {
                $fields[$field] = [];
            }
            $this->showFields = $fields;
        }
        $fields = $this->showFields;
        if (!is_array($fields)) {
            throw CrudException::forInvalidObjectType('array', $fields);
        } else {
            $fields = [];
            foreach ($this->showFields as $field => $props) {
                if (is_string($props)) {
                    $field = $props;
                    $props = [];
                }
                if (is_array($props)) {
                    $props = FieldProperties::make($props);
                }
                if (!($props instanceof FieldProperties)) {
                    throw CrudException::forInvalidObjectType(FieldProperties::class, $props);
                }
                $fields[$field] = $props;
            }
            $this->showFields = $fields;
        }

        //--[ optionFields ]--

        if (!empty($this->optionFields) and is_string($this->optionFields)) {
            $fields = [];
            foreach (explode('|', $this->optionFields) as $field) {
                $fields[$field] = [];
            }
            $this->optionFields = $fields;
        }
        $fields = $this->optionFields;
        if (!is_array($fields)) {
            throw CrudException::forInvalidObjectType('array', $fields);
        } else {
            $fields = [];
            foreach ($this->optionFields as $field => $props) {
                if (is_string($props)) {
                    $field = $props;
                    $props = [];
                }
                if (is_array($props)) {
                    $props = FieldProperties::make($props);
                }
                if (!($props instanceof FieldProperties)) {
                    throw CrudException::forInvalidObjectType(FieldProperties::class, $props);
                }
                $fields[$field] = $props;
            }
            $this->optionFields = $fields;
        }
    }
}
