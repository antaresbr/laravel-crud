<?php

namespace Antares\Crud\Metadata\Field;

use Antares\Crud\Metadata\AbstractMetadata;

class GridFieldProperties extends AbstractMetadata
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
            'gridCols' => [
                'type' => 'integer',
                'required' => false,
                'nullable' => true,
            ],
        ];
    }

    /**
     * @see AbstractMetadata::toArray()
     */
    public function toArray($onlyDefinedProperties = true)
    {
        return parent::toArray($onlyDefinedProperties);
    }
}
