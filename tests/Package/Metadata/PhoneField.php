<?php

namespace Antares\Tests\Package\Metadata;

class PhoneField extends AppField
{
    /**
     * Make a brand new object
     *
     * @param array $data
     * @return static
     */
    public static function make(array $data)
    {
        $data = array_merge([
            'length'  => 20,
            'mask'    => '+00 (00) 00000-0000||+00 (00) 0000-0000',
            'uicCols' => 2,
        ], $data);
        $data['type'] = 'phone';

        return parent::make($data);
    }
}
