<?php

namespace Antares\Crud\Metadata\Layout;

class HBox extends AbstractContainer
{
    /**
     * Class constructor
     *
     * @param array $data
     * @param array $prototypes
     */
    public function __construct(array $data = [])
    {
        $data['type'] = 'hbox';

        parent::__construct($data);
    }

    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        $prototype = parent::prototype();
        $prototype['cols']['default'] = 12;

        return $prototype;
    }
}
