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

    public function __set(string $name, $value)
    {
        return $this->options->set($name, $value);
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
    public function offsetExists($key): bool
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
    public function offsetSet($key, $value): void
    {
        $this->options->offsetSet($key, $value);
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key): void
    {
        $this->options->offsetUnset($key);
    }

    //-- implements : Countable

    /**
     * Get items count
     *
     * @return int
     */
    public function count(): int
    {
        return $this->options->count();
    }

    //-- IteratorAggregate, Traversable

    /**
     * Get itarator
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
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
     * @param array $data
     * @param array $prototypes
     */
    public function __construct(array $data = [])
    {
        $this->customDefaults($data);

        $this->options = Options::make($data, $this->prototype())->validate();

        $this->customValidations();
    }

    /**
     * Custom defaults to be made in object construct process
     *
     * @param array $data
     * @return void
     */
    protected function customDefaults(array &$data)
    {
        foreach ($this->prototype() as $key => $props) {
            if (isset($props['default']) and !isset($data[$key])) {
                $data[$key] = $props['default'];
            }
        }
    }

    /**
     * Custom validations to be made in object construct process
     *
     * @return void
     */
    protected function customValidations()
    {
        //-- To be implemented in descendent classes, if so.
    }

    /**
     * Get array representation of this object
     *
     * @param bool $onlyDefinedProperties
     * @return array
     */
    public function toArray($onlyDefinedProperties = false)
    {
        $r = [];
        foreach (array_keys($this->prototype()) as $key) {
            if (!$onlyDefinedProperties or $this->options->has($key)) {
                $r[$key] = $this->{$key};
            }
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
