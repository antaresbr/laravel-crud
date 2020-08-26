<?php

namespace Antares\Crud\Metadata;

class TableDataSource extends DataSource
{
    /**
     * Make a brand new object
     *
     * @param array $data
     * @return staic
     */
    public static function make(array $data)
    {
        $data['type'] = 'table';

        return parent::make($data);
    }
}
