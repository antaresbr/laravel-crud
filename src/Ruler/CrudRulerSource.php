<?php

namespace Antares\Crud\Ruler;

use Antares\Support\Arr;
use Antares\Support\Options;

class CrudRulerSource
{
    /**
     * Prototype for ruler source objects
     */
    public const PROTOTYPE = [
        'keyName' => ['type' => 'string', 'required' => true, 'nullable' => false],
        'basicRules' => ['type' => 'string|array', 'default' => []],
        'uniqueRule' => ['type' => ['string', '\Illuminate\Validation\Rules\Unique']],
    ];

    /**
     * Options object for this ruler source.
     *
     * @var \Antares\Support\Options
     */
    protected $options;

    public function __isset(string $name)
    {
        return $this->options->has($name);
    }

    public function __get(string $name)
    {
        return $this->options->get($name);
    }

    public function __call(string $name, $params)
    {
        return call_user_func_array([$this->options, $name], $params);
    }

    /**
     * Make a brand new object
     *
     * @param array $data See static::PROTOTYPE
     * @return staic
     */
    public static function make(array $data)
    {
        $r = new static;
        $r->options = Options::make($data, static::PROTOTYPE)->validate();
        $r->options->set('basicRules', Arr::arrayed($r->options->basicRules));

        return $r;
    }
}
