<?php

namespace Antares\Crud;

use Antares\Support\ArrayHandler\Arr;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Pagination\AbstractPaginator;

class CrudPagination implements Arrayable
{
    /**
     * Data properties
     *
     * @var array
     */
    protected $data = [];

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : null;
    }

    public function toArray()
    {
        return $this->data;
    }

    /**
     * Get the target method or property value, if exists, from an object
     *
     * @param mixed $source
     * @param string $target
     * @return mixed|null
     */
    protected static function getValue($source, $target)
    {
        if (is_object($source)) {
            if (method_exists($source, $target)) {
                return $source->$target();
            } elseif (property_exists($source, $target)) {
                return $source->$target;
            }
        } else {
            if (is_array($source)) {
                return Arr::get($source, $target);
            }
        }
        return null;
    }

    /**
     * Get the prefix to be used in source array
     *
     * @param mixed $source
     * @return string
     */
    protected static function getPrefix($source)
    {
        $prefix = '';
        if (is_array($source)) {
            if (Arr::has($source, 'data.pagination')) {
                $prefix = 'data.pagination.';
            } elseif (Arr::has($source, 'pagination')) {
                $prefix = 'pagination.';
            }
        }
        return $prefix;
    }

    /**
     * Create pagination object from supplied source
     *
     * @param mixed $source
     * @return static
     */
    public static function make($source)
    {
        $r = new static;

        if ($source instanceof AbstractPaginator) {
            $r->current_page = static::getValue($source, 'currentPage');
            $r->last_page = static::getValue($source, 'lastPage');
            $r->per_page = static::getValue($source, 'perPage');
            $r->from = static::getValue($source, 'firstItem');
            $r->to = static::getValue($source, 'lastItem');
            $r->total = static::getValue($source, 'total');
        } else {
            if (is_array($source)) {
                $prefix = static::getPrefix($source);
                $r->current_page = Arr::get($source, $prefix . 'current_page');
                $r->last_page = Arr::get($source, $prefix . 'last_page');
                $r->per_page = Arr::get($source, $prefix . 'per_page');
                $r->from = Arr::get($source, $prefix . 'from');
                $r->to = Arr::get($source, $prefix . 'to');
                $r->total = Arr::get($source, $prefix . 'total');
            }
        }

        return $r;
    }
}
