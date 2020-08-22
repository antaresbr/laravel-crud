<?php

namespace Antares\Crud\Metadata;

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
    protected function customDefaults()
    {
        if ($this->type == 'picklist') {
            if (empty($this->sourceKey)) {
                $this->sourceKey = 'key';
            }
            if (empty($this->showFields)) {
                $this->showFields = ['label'];
            }
            if (empty($this->optionFields)) {
                $this->optionFields = ['label'];
            }
        }

        if ($this->type == 'table') {
            if (empty($this->sourceKey)) {
                $this->sourceKey = 'id';
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
        if (!empty($this->showFields) and is_string($this->showFields)) {
            $this->showFields = explode('|', $this->showFields);
        }

        if (!empty($this->optionFields) and is_string($this->optionFields)) {
            $this->optionFields = explode('|', $this->optionFields);
        }
    }
}
