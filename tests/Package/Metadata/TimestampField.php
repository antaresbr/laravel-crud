<?php

namespace Antares\Tests\Package\Metadata;

class TimestampField extends AppField
{
    /**
     * Make a brand new object
     *
     * @param array $data
     * @return static
     */
    public static function make(array $data)
    {
        $data['type'] = 'timestamp';

        return parent::make($data);
    }
}
