<?php

namespace Antares\Crud\Metadata\Layout;

class Fieldset extends AbstractContainer
{
    /**
     * Class constructor
     *
     * @param array $data
     * @param array $prototypes
     */
    public function __construct(array $data = [])
    {
        $data['type'] = 'fieldset';

        parent::__construct($data);
    }
}
