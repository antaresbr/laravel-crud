<?php

namespace Antares\Crud;

use Antares\Crud\CrudException;
use Antares\Foundation\Arr;
use Antares\Foundation\Options\Options;
use Antares\Foundation\Str;

class CrudRuler
{
    /**
     * Field name
     *
     * @var array
     */
    protected $field;

    /**
     * Rules array
     *
     * @var array
     */
    protected $rules;

    /**
     * Get rules from this ruler
     *
     * Prototype/Default options:
     * [
     *     'includeUniqueRules' => ['type' => 'boolean', 'default' => true],
     *     'additionalRules' => ['type' => 'string|array', 'default' => []],
     *     'exceptRules' => ['type' => 'string|array', 'default' => []],
     * ]
     *
     * @param array $options
     * @param bool $arreyed
     * @return array
     */
    public function getRules(array $options = [], bool $arreyed = false)
    {
        if (empty($this->rules)) {
            return [];
        }

        $opt = Options::make($options, [
            'includeUniqueRules' => ['type' => 'boolean', 'default' => true],
            'additionalRules' => ['type' => 'string|array', 'default' => []],
            'exceptRules' => ['type' => 'string|array', 'default' => []],
        ])->validate();

        $rules = $this->rules;

        if (!empty($opt->additionalRules)) {
            $rules = array_merge($rules, Arr::arrayed($opt->additionalRules));
        }

        if (!empty($opt->exceptRules)) {
            $rules = array_diff($rules, Arr::arrayed($opt->exceptRules));
        }

        foreach ($rules as $key => &$value) {
            $value = ltrim($value);
            if ($value == 'unique:') {
                $value = 'unique';
            }
            if ($value == 'unique' or Str::startsWith($value, 'unique:')) {
                if (!$opt->includeUniqueRules) {
                    unset($rules[$key]);
                }
            }
        }

        return $arreyed ? [$this->field => array_values($rules)] : array_values($rules);
    }

    /**
     * Make a brand new object
     *
     * @param string $field
     * @param array|string $rules
     * @return static
     */
    public static function make(string $field, $rules = [])
    {
        if (is_null($field) or trim($field) == '') {
            CrudException::forInvalidFieldName($field);
        }

        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }
        if (!is_array($rules)) {
            CrudException::forInvalidObjectType('array', $rules);
        }

        $r = new static;
        $r->field = $field;
        $r->rules = $rules;

        return $r;
    }
}
