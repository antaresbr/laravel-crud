<?php

namespace Antares\Crud\Metadata\Field;

use Antares\Crud\CrudException;
use Antares\Crud\Metadata\AbstractMetadata;

class UicProperties extends AbstractMetadata
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return [
            'action' => [
                'type' => 'array',
                'required' => true,
                'nullable' => false,
                'default' => [],
            ],
            'conditional' => [
                'type' => 'array',
                'required' => true,
                'nullable' => false,
                'default' => [],
            ],
        ];
    }
    
    /**
     * @see AbstractMetadata::customValidations()
     *
     * @return void
     */
    protected function customValidations()
    {
        parent::customValidations();
        
        //--[ action ]--
        if (!is_array($this->action)) {
            throw CrudException::forInvalidObjectType('array', $this->action);
        } else {
            $items = [];
            foreach ($this->action as $item) {
                if (is_array($item)) {
                    $item = UicActionProperty::make($item);
                }
                if (!($item instanceof UicActionProperty)) {
                    throw CrudException::forInvalidObjectType(UicActionProperty::class, $item);
                }
                $items[] = $item;
            }
            $this->action = $items;
        }

        //--[ conditional ]--
        if (!is_array($this->conditional)) {
            throw CrudException::forInvalidObjectType('array', $this->conditional);
        } else {
            $items = [];
            foreach ($this->conditional as $item) {
                if (is_array($item)) {
                    $item = UicConditionalProperty::make($item);
                }
                if (!($item instanceof UicConditionalProperty)) {
                    throw CrudException::forInvalidObjectType(UicConditionalProperty::class, $item);
                }
                $items[] = $item;
            }
            $this->conditional = $items;
        }
    }

}
