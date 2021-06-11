<?php

namespace Antares\Crud\Metadata\Layout;

use Antares\Crud\CrudException;

class TabGroup extends AbstractContainer
{
    /**
     * Class constructor
     *
     * @param array $data
     * @param array $prototypes
     */
    public function __construct(array $data = [])
    {
        $data['type'] = 'tabgroup';

        parent::__construct($data);
    }

    /**
     * @see AbstractMetadata::customValidations()
     *
     * @return void
     */
    protected function customValidations()
    {
        $children = $this->children;
        if (!empty($children)) {
            foreach ($children as $child) {
                if (!($child instanceof Tab)) {
                    throw CrudException::forInvalidObjectType(Tab::class, $child);
                }
            }
        }
    }
}
