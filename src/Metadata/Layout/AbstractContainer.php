<?php

namespace Antares\Crud\Metadata\Layout;

use Antares\Crud\CrudException;

abstract class AbstractContainer extends AbstractLayout
{
    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return array_merge(parent::prototype(), [
            'title' => [
                'type' => 'string',
                'required' => false,
                'nullable' => true,
            ],
            'border' => [
                'type' => 'boolean|string',
                'required' => false,
                'nullable' => true,
                'default' => false,
            ],
            'children' => [
                'type' => 'array',
                'required' => false,
                'nullable' => true,
            ],
        ]);
    }

    /**
     * @see AbstractMetadata::customValidates()
     *
     * @return void
     */
    protected function customValidates()
    {
        $children = $this->children;
        if (!empty($children)) {
            foreach ($children as $child) {
                if (!($child instanceof AbstractLayout)) {
                    throw CrudException::forInvalidObjectType(AbstractLayout::class, $child);
                }
            }
        }
    }
}
