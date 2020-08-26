<?php

namespace Antares\Crud\Metadata;

class Field extends AbstractField
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        $p = array_merge(
            [
                'name' => [
                    'type' => 'string',
                    'required' => true,
                    'nullable' => false,
                ],
            ],
            parent::prototype(),
            [
                'gridCols' => [
                    'type' => 'integer',
                    'required' => false,
                    'nullable' => true,
                ],
            ]
        );

        $p['type']['required'] = true;
        $p['type']['nullable'] = false;

        $p['unsigned']['default'] = true;

        $p['uic']['nullable'] = false;
        $p['uic']['default'] = 'text';

        $p['uicCols']['nullable'] = false;
        $p['uicCols']['default'] = 1;

        $p['disabled']['default'] = false;

        $p['hidden']['default'] = false;

        return $p;
    }
}
