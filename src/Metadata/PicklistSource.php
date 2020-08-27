<?php

namespace Antares\Crud\Metadata;

class PicklistSource extends DataSource
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        $prototype = parent::prototype();
        $prototype['type']['required'] = false;
        $prototype['type']['nullable'] = true;
        $prototype['type']['default'] = 'picklist';

        return $prototype;
    }
}
