<?php

namespace Antares\Crud\Metadata;

use Antares\Support\Options;
use ArrayAccess;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

abstract class AbstractMetadata implements ArrayAccess, Countable, IteratorAggregate, Traversable, JsonSerializable
{
    /**
     * Get prototype array definitions
     *
     * @return array
     */
    abstract protected function prototype();

    /**
     * Options object for this ruler source.
     *
     * @var \Antares\Support\Options
     */
    protected $options;

    public function __isset(string $name)
    {
        return $this->options->__isset($name);
    }

    public function __get(string $name)
    {
        return $this->options->__get($name);
    }

    public function __call(string $name, $params)
    {
        return call_user_func_array([$this->options, $name], $params);
    }

    //--[ implements : start ]--

    //-- implements : ArrayAccess

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->options->offsetExists($key);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->options->offsetGet($key);
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->options->offsetSet($key, $value);
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->options->offsetUnset($key);
    }

    //-- implements : Countable

    /**
     * Get items count
     *
     * @return integer
     */
    public function count()
    {
        return $this->options->count();
    }

    //-- IteratorAggregate, Traversable

    /**
     * Get itarator
     *
     * @return ArrayItarator
     */
    public function getIterator()
    {
        return $this->options->getIterator();
    }

    //-- JsonSerializable

    /**
     * Get items data itself for serialization
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    //--[ implements : end ]--

    /**
     * Class constructor
     *
     * @param array $data Data to be used in this object
     * @param array $prototypes The protorype applied to this object
     */
    public function __construct(array $data = [])
    {
        $this->options = Options::make($data, $this->prototype())->validate();
        $this->customDefaults();
        $this->customValidates();
    }

    /**
     * Custom defaults to be made in object construct process
     *
     * @return void
     */
    protected function customDefaults()
    {
        //-- To be implemented in descendent classes, if so.
    }

    /**
     * Custom validations to be made in object construct process
     *
     * @return void
     */
    protected function customValidates()
    {
        //-- To be implemented in descendent classes, if so.
    }

    /**
     * Get array representation of this object
     *
     * @return array
     */
    public function toArray()
    {
        $r = [];
        foreach (array_keys($this->prototype()) as $key) {
            $r[$key] = $this->{$key};
        }
        return $r;
    }

    /**
     * Make a brand new object and returns its array representation
     *
     * @param array $data
     * @return staic
     */
    public static function makeArray(array $data)
    {
        return static::make($data)->toArray();
    }

    /**
     * Make a brand new object
     *
     * @param array $data
     * @return staic
     */
    public static function make(array $data)
    {
        return new static($data);
    }
}
