<?php

namespace Antares\Crud\Ruler;

use Antares\Support\Arr;
use Antares\Support\Options;
use Antares\Support\Str;
use Illuminate\Validation\Rules\Unique;

class CrudRuler
{
    /**
     * Source options object for this ruler.
     *
     * @var \Antares\Crud\Ruler\CrudRulerSource
     */
    protected $source;

    /**
     * Build the primary key rules base on the array property 'source', with the attributes as follow.
     *
     * Prototype/Default options:
     * [
     *     'includeUniqueRule' => ['type' => 'boolean', 'default' => true],
     *     'uniqueExceptId' => ['type' => 'mixed', 'default' => null],
     *     'additionalRules' => ['type' => 'string|array', 'default' => []],
     *     'exceptRules' => ['type' => 'string|array', 'default' => []],
     * ]
     *
     * @param array $options
     * @return array
     */
    public function getRules(array $options = [])
    {
        if (empty($this->source)) {
            return [];
        }

        $opt = Options::make($options, [
            'includeUniqueRule' => ['type' => 'boolean', 'default' => true],
            'uniqueExceptId' => ['type' => 'mixed', 'default' => null],
            'additionalRules' => ['type' => 'string|array', 'default' => []],
            'exceptRules' => ['type' => 'string|array', 'default' => []],
        ])->validate();

        $rules = empty($this->source->basicRules) ? [] : $this->source->basicRules;

        if ($opt->includeUniqueRule) {
            $rule = $this->source->uniqueRule;
            if (!empty($rule)) {
                if ($rule instanceof Unique) {
                    $rule->ignore($opt->uniqueExceptId, $this->source->keyName);
                } else {
                    $rule = Str::start($this->source->uniqueRule, 'unique:');
                    if (!is_null($opt->uniqueExceptId)) {
                        //$rule .= Str::start($opt->uniqueExceptId, ",{$this->source->keyName},");
                        $rule .= Str::start($opt->uniqueExceptId, ',,');
                    }
                }
                $rules[] = $rule;
            }
        }

        if (!empty($opt->additionalRules)) {
            $rules = array_merge($rules, Arr::arrayed($opt->additionalRules));
        }

        if (!empty($opt->exceptRules)) {
            $rules = array_diff($rules, Arr::arrayed($opt->exceptRules));
        }

        return [
            $this->source->keyName => $rules,
        ];
    }

    /**
     * Make a brand new object
     *
     * @param array $source See source prototype in \Antares\Crud\CrudValidationRuler::setSource()
     * @return static
     */
    public static function make(array $source = [])
    {
        $r = new static;
        $r->source = CrudRulerSource::make($source);

        return $r;
    }
}
