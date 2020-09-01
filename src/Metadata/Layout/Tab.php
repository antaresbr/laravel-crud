<?php

namespace Antares\Crud\Metadata\Layout;

class Tab extends AbstractContainer
{
    /**
     * Class constructor
     *
     * @param array $data
     * @param array $prototypes
     */
    public function __construct(array $data = [])
    {
        $data['type'] = 'tab';

        parent::__construct($data);
    }
}
