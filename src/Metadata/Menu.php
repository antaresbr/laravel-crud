<?php

namespace Antares\Crud\Metadata;

use Antares\Crud\CrudException;

class Menu extends AbstractMetadata
{
    public static $prototype =[
        'id' => [
            'type' => 'integer',
            'required' => false,
            'nullable' => true,
        ],
        'path' => [
            'type' => 'string',
            'required' => true,
            'nullable' => false,
        ],
        'description' => [
            'type' => 'string',
            'required' => true,
            'nullable' => false,
        ],
        'type' => [
            'type' => 'integer',
            'required' => true,
            'nullable' => false,
            'default' => 0,
            'values' => [0, 1, 2],
        ],
        'enabled' => [
            'type' => 'boolean|integer|string',
            'required' => true,
            'nullable' => false,
            'default' => true,
        ],
        'style' => [
            'type' => 'string',
            'required' => false,
            'nullable' => true,
        ],
        'action' => [
            'type' => 'string',
            'required' => false,
            'nullable' => true,
        ],
        'component' => [
            'type' => 'string',
            'required' => false,
            'nullable' => true,
        ],
        'vars' => [
            'type' => 'string',
            'required' => false,
            'nullable' => true,
        ],
        'api' => [
            'type' => 'string',
            'required' => false,
            'nullable' => true,
        ],
        'items' => [
            'type' => 'array',
            'required' => false,
            'nullable' => true,
            'default' => [],
        ],
    ];

    /**
     * @see AbstractMetadata::prototype()
     */
    protected function prototype()
    {
        return static::$prototype;
    }

    /**
     * @see AbstractMetadata::customValidations()
     *
     * @return void
     */
    protected function customValidations()
    {
        $items = $this->items;
        if (!empty($items)) {
            foreach ($items as $item) {
                if (is_string($item)) {
                    continue;
                }
                if (!($item instanceof static)) {
                    throw CrudException::forInvalidObjectType(static::class, $item);
                }
            }
        }
    }

    /**
     * Get metadata from menu object or array
     *
     * @param mixed $menu
     * @return array
     */
    public static function menuToMetadata($menu)
    {
        if (is_object($menu)) {
            $menu = method_exists($menu, 'toArray') ? $menu->toArray() : (array) $menu;
        }

        return [
            'id' => array_key_exists('id', $menu) ? $menu['id'] : null,
            'path' => $menu['path'],
            'description' => $menu['description'],
            'type' => array_key_exists('type', $menu) ? $menu['type'] : static::$prototype['type']['default'],
            'enabled' => array_key_exists('enabled', $menu) ? filter_var($menu['enabled'], FILTER_VALIDATE_BOOLEAN) : static::$prototype['enabled']['default'],
            'style' => array_key_exists('style', $menu) ? $menu['style'] : null,
            'action' => array_key_exists('action', $menu) ? $menu['action'] : null,
            'component' => array_key_exists('component', $menu) ? $menu['component'] : null,
            'vars' => array_key_exists('vars', $menu) ? $menu['vars'] : null,
            'api' => array_key_exists('api', $menu) ? $menu['api'] : null,
            'items' => array_key_exists('items', $menu) ? $menu['items'] : static::$prototype['items']['default'],
        ];
    }
}
