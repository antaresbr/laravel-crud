<?php

namespace Antares\Crud\Metadata\Field;

use Antares\Crud\Metadata\AbstractMetadata;

class UicConditionalProperty extends AbstractMetadata
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return [
            'property' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
                'values' => ['default', 'disabled', 'hidden'],
            ],
            'condition' => [
                'type' => 'string',
                'required' => true,
                'nullable' => false,
            ],
            'onTrue' => [
                'type' => 'mixed',
                'required' => true,
                'nullable' => false,
            ],
            'onFalse' => [
                'type' => 'mixed',
                'required' => false,
                'nullable' => true,
                'default' => '__not-used__',
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
        
        //--[ onFalse ]--
        if (!isset($this->onFalse)) {
            $this->onFalse = '__not-used__';
        }
    }
}
