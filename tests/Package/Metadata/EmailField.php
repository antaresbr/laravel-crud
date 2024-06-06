<?php

namespace Antares\Tests\Package\Metadata;

class EmailField extends AppField
{
    /**
     * Make a brand new object
     *
     * @param array $data
     * @return static
     */
    public static function make(array $data)
    {
        $data = array_merge(
            [
                'uicCols' => 5,
                'label' => 'Email',
            ],
            $data
        );
        $data['type'] = 'email';

        return parent::make($data);
    }
}
