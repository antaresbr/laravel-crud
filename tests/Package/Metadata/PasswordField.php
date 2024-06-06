<?php

namespace Antares\Tests\Package\Metadata;

class PasswordField extends AppField
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
                'uicCols' => 2,
                'label'   => 'Password',
                'length'  => 255,
            ],
            $data
        );

        $data['type'] = 'text';
        $data['uic']  = 'password';

        return parent::make($data);
    }
}
