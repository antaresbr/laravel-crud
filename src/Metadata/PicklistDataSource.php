<?php

namespace Antares\Crud\Metadata;

class PicklistDataSource extends DataSource
{
    /**
     * Make a brand new object
     *
     * @param array $data
     * @return staic
     */
    public static function make(array $data)
    {
        $data['type'] = 'picklist';

        return parent::make($data);
    }
}
