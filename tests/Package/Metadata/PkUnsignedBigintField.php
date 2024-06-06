<?php

namespace Antares\Tests\Package\Metadata;

class PkUnsignedBigintField extends BigintField
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
                'label' => 'ID',
                'tooltip' => 'Generated ID',
                'uicCols' => 2,
                'default' => null,
                'disabled' => true,
            ],
            $data
        );
        $data['type'] = 'bigint';
        $data['unsigned'] = 'true';

        return parent::make($data);
    }
}
