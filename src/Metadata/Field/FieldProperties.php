<?php

namespace Antares\Crud\Metadata\Field;

class FieldProperties extends AbstractField
{
    /**
     * @see AbstractMetadata::toArray()
     */
    public function toArray($onlyDefinedProperties = true)
    {
        return parent::toArray($onlyDefinedProperties);
    }
}
