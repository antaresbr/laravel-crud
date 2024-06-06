<?php

namespace Antares\Tests\Package\Metadata;

class TextField extends AppField
{
    /**
     * Make a brand new object
     *
     * @param array $data
     * @return static
     */
    public static function make(array $data)
    {
        $data['type'] = 'text';

        return parent::make($data);
    }
}
