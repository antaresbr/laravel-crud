<?php

namespace Antares\Crud\Metadata\DataSource;

use Antares\Crud\CrudException;
use Antares\Crud\Metadata\AbstractMetadata;
use Antares\Crud\Metadata\Field\FieldProperties;

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
            'model' => [
                'type' => 'boolean|Antares\Crud\CrudModel',
                'required' => false,
                'nullable' => true,
            ],
            'sourceKey' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
            ],
            'metadata' => [
                'type' => 'array',
                'required' => false,
                'nullable' => true,
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
            'assignFields' => [
                'type' => 'string|array',
                'required' => false,
                'nullable' => true,
            ],
            'frame' => [
                'type' => 'Antares\Crud\Metadata\Frame',
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
            if (!empty($data['model'])) {
                $model = new $data['model']();
                if (empty($data['sourceKey']) and !empty($model->primaryKey)) {
                    $data['sourceKey'] = $model->primaryKey;
                }
            }
            if (empty($data['sourceKey'])) {
                $data['sourceKey'] = 'id';
            }
            if (empty($data['api']) and !empty($data['id'])) {
                $data['api'] = $data['id'];
            }
        }

        parent::customDefaults($data);
    }

    /**
     * @see AbstractMetadata::customValidations()
     *
     * @return void
     */
    protected function customValidations()
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

        //--[ assignFields ]--

        if (empty($this->assignFields)) {
            $this->assignFields = null;
        } elseif (is_string($this->assignFields)) {
            $assigns = [];
            foreach (explode('|', $this->assignFields) as $assignItem) {
                $assign = explode(':', $assignItem);
                if (count($assign) != 2 or empty($assign[0]) or empty($assign[1])) {
                    throw CrudException::forInvalidFieldAssign($assignItem);
                }
                $assigns[$assign[0]] = $assign[1];
            }
            $this->assignFields = $assigns;
        }
        if (!is_null($this->assignFields)) {
            if (!is_array($this->assignFields)) {
                throw CrudException::forInvalidObjectType('array', $this->assignFields);
            } else {
                foreach ($this->assignFields as $field => $source) {
                    if (!is_string($field) or empty($field) or !is_string($source) or empty($source)) {
                        throw CrudException::forInvalidFieldAssign(['field' => $field, 'source' => $source]);
                    }
                }
            }
        }
    }
}
