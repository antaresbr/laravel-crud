<?php

namespace Antares\Crud;

use Antares\Support\Arr;
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
     * @param mixed $default
     * @return mixed|null
     */
    protected static function getValue($source, $target, $default = null)
    {
        if (is_object($source)) {
            if (method_exists($source, $target)) {
                return $source->$target();
            } elseif (property_exists($source, $target)) {
                return $source->$target;
            }
        } else {
            if (is_array($source)) {
                return Arr::get($source, $target, $default);
            }
        }
        return $default;
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
            $r->currentPage = static::getValue($source, 'currentPage');
            $r->lastPage = static::getValue($source, 'lastPage');
            $r->perPage = static::getValue($source, 'perPage');
            $r->from = static::getValue($source, 'firstItem');
            $r->to = static::getValue($source, 'lastItem');
            $r->total = static::getValue($source, 'total');
            $r->targetPage = static::getValue($source, 'targetPage');
        } else {
            if ($source instanceof \Illuminate\Database\Eloquent\Collection) {
                dd($source);
                $r->currentPage = static::getValue($source, 'currentPage', 1);
                $r->lastPage = static::getValue($source, 'lastPage', $r->currentPage);
                $r->perPage = static::getValue($source, 'perPage', 0);
                $r->from = static::getValue($source, 'from', 1);
                $r->to = static::getValue($source, 'to', $source->count());
                $r->total = static::getValue($source, 'total', $source->count());
                $r->targetPage = static::getValue($source, 'targetPage');
            } else {
                if (is_array($source)) {
                    $qItems = empty($source['items']) ? 0 : count($source['items']);
                    $prefix = static::getPrefix($source);
                    $r->currentPage = Arr::get($source, $prefix . 'currentPage', 1);
                    $r->lastPage = Arr::get($source, $prefix . 'lastPage', $r->currentPage);
                    $r->perPage = Arr::get($source, $prefix . 'perPage', 0);
                    $r->from = Arr::get($source, $prefix . 'from', ($qItems > 0) ? 1 : 0);
                    $r->to = Arr::get($source, $prefix . 'to', $qItems);
                    $r->total = Arr::get($source, $prefix . 'total', $qItems);
                    $r->targetPage = Arr::get($source, $prefix . 'targetPage');
                }
            }
        }

        return $r;
    }
}
