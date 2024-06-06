<?php

namespace Antares\Tests\Package\Metadata;

class BigintField extends AppField
{
    /**
     * Make a brand new object
     *
     * @param array $data
     * @return static
     */
    public static function make(array $data)
    {
        $data['type'] = 'bigint';

        return parent::make($data);
    }
}
