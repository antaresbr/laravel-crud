<?php

namespace Antares\Crud\Metadata;

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
