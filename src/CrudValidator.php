<?php

namespace Antares\Crud;

use Antares\Support\Arr;
use Antares\Support\Str;
use Antares\Support\Options;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use function PHPUnit\Framework\isNull;

class CrudValidator
{
    /**
     * Table name
     *
     * @var string
     */
    public $table;

    /**
     * Primary key field name
     *
     * @var string
     */
    public $primaryKey;

    /**
     * Get default rules
     *
     * @return array
     */
    public function defaultRules()
    {
        if (property_exists($this, 'defaultRules') and !empty($this->defaultRules)) {
            return $this->defaultRules;
        }

        return [];
    }

    /**
     * Get the validation rules for supplied action
     *
     * @param string $action
     * @param string $pkOptions
     * @return array
     */
    public function getActionRules(string $action = '', $options = [])
    {
        $rules = [];

        $action = trim($action);
        if ($action != '') {
            $method = 'get' . str_replace(' ', '', ucwords(strtolower($action))) . 'Rules';
            if (method_exists($this, $method)) {
                $rules = method_exists($this, $method) ? $this->$method($options) : [];
            }
        }

        foreach ($rules as &$item) {
            if (is_string($item)) {
                $item = explode('|', $item);
            }
        }

        return $rules;
    }

    /**
     * Get the validation rules for supplied action as metadata
     *
     * @param string $action
     * @param string $options
     * @return array
     */
    public function getRulesAsMetadata(string $action = '', $options = [])
    {
        $source = $this->getActionRules($action, $options);

        $rules = [];
        foreach ($source as $sourceKey => $sourceValue) {
            if (is_string($sourceValue)) {
                $sourceValue = explode('|', $sourceValue);
            }

            $value = [];
            foreach ($sourceValue as $valueItem) {
                if (is_object($valueItem)) {
                    if (method_exists($valueItem, 'toArray')) {
                        $valueItem = $valueItem->toArray();
                    } else {
                        continue;
                    }
                }
                $value[] = $valueItem;
            }

            $rules[$sourceKey] = $value;
        }

        return $rules;
    }

    /**
     * Get primary key rules
     *
     * @param array $options
     * @return array
     */
    public function getPrimaryKeyRules(array $options = [])
    {
        if ($this->primaryKey) {
            $defaultRules = $this->defaultRules();
            if (array_key_exists($this->primaryKey, $defaultRules)) {
                $pkOptions = array_key_exists($this->primaryKey, $options) ? $options[$this->primaryKey] : [];
                return CrudRuler::make($this->primaryKey, $defaultRules[$this->primaryKey])->getRules($pkOptions, true);
            }
        }
        return [];
    }

    /**
     * Get all rules
     *
     * @param array $options
     * @return array
     */
    public function getAllRules(array $options = [])
    {
        $allRules = [];
        foreach ($this->defaultRules() as $fieldName => $fieldRules) {
            $fieldOptions = array_key_exists($fieldName, $options) ? $options[$fieldName] : [];
            $allRules[$fieldName] = CrudRuler::make($fieldName, $fieldRules)->getRules($fieldOptions);
        }
        return $allRules;
    }

    /**
     * Get index rules options
     *
     * @return array
     */
    public function indexRulesOptions()
    {
        return [];
    }

    /**
     * Get rules for index action
     *
     * @param array $options
     * @return array
     */
    public function getIndexRules(array $options = [])
    {
        return [];
    }

    /**
     * Get store rules options
     *
     * @return array
     */
    public function storeRulesOptions()
    {
        $options = [];
        if ($this->primaryKey) {
            $options[$this->primaryKey] = [
                'exceptRules' => 'required',
                'additionalRules' => 'nullable',
            ];
        }
        return $options;
    }

    /**
     * Get rules for store action
     *
     * @param array $options
     * @return array
     */
    public function getStoreRules(array $options = [])
    {
        $options = array_merge($this->storeRulesOptions(), $options);
        return $this->getAllRules($options);
    }

    /**
     * Get show rules options
     *
     * @return array
     */
    public function showRulesOptions()
    {
        $options = [];
        if ($this->primaryKey) {
            $options[$this->primaryKey] = [
                'includeUniqueRules' => false,
            ];
        }
        return $options;
    }

    /**
     * Get rules for show action
     *
     * @param array $options
     * @return array
     */
    public function getShowRules(array $options = [])
    {
        $options = array_merge($this->showRulesOptions(), $options);
        return $this->getPrimaryKeyRules($options);
    }

    /**
     * Get update rules options
     *
     * @return array
     */
    public function updateRulesOptions()
    {
        return [];
    }

    /**
     * Get rules for update action
     *
     * @param array $options
     * @return array
     */
    public function getUpdateRules(array $options = [])
    {
        $options = array_merge($this->updateRulesOptions(), $options);
        return $this->getAllRules($options);
    }

    /**
     * Get destroy rules options
     *
     * @return array
     */
    public function destroyRulesOptions()
    {
        $options = [];
        if ($this->primaryKey) {
            $options[$this->primaryKey] = [
                'includeUniqueRules' => false,
            ];
        }
        return $options;
    }

    /**
     * Get rules for destroy action
     *
     * @param array $options
     * @return array
     */
    public function getDestroyRules(array $options = [])
    {
        $options = array_merge($this->destroyRulesOptions(), $options);
        return $this->getPrimaryKeyRules($options);
    }

    /**
     * The current validator object
     *
     * @var \Illuminate\Contracts\Validation\Validator
     */
    protected $validator;

    /**
     * The current validator object accessor
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validator()
    {
        return $this->validator;
    }

    /**
     * Check if the current validator object has errors
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return $this->validator() ? empty($this->validator()->errors()) : false;
    }

    /**
     * Get current validator object error messages
     *
     * @return array
     */
    public function errors()
    {
        return $this->validator() ? $this->validator()->errors()->toArray() : [];
    }

    /**
     * Apply the validation rules from an action to supplied data
     *
     * @param string $action
     * @param array $data
     * @param array $oldData
     * @param array $pkOptions
     * @param array $messages
     * @param array $customAttributes
     * @return boolean
     */
    public function validate($action, $data, $oldData, $options = [], $messages = [], $customAttributes = [])
    {
        $this->validator = null;
        $rules = $this->getActionRules($action, $options);
        if (!empty($rules)) {
            foreach ($rules as $fieldName => &$fieldRules) {
                foreach ($fieldRules as &$fieldRule) {
                    if (is_string($fieldRule) and ($fieldRule == 'unique' or Str::startsWith($fieldRule, 'unique:'))) {
                        if ($fieldRule == 'unique') {
                            $fieldRule = '';
                        }
                        if (Str::startsWith($fieldRule, 'unique:')) {
                            $fieldRule = Str::replaceFirst('unique:', '', $fieldRule);
                        }
                        $pieces = explode(',', $fieldRule);
                        $ukTable = !empty($pieces[0]) ? $pieces[0] : $this->table;
                        $ukField = !empty($pieces[1]) ? $pieces[1] : $fieldName;
                        $ukExcept = isset($pieces[2]) ? $pieces[2] : null;
                        if (is_null($ukExcept) and isset($oldData[$ukField])) {
                            $ukExcept = $oldData[$ukField];
                        }
                        $fieldRule = Rule::unique($ukTable, $ukField);
                        if (!is_null($ukExcept)) {
                            $fieldRule->ignore($ukExcept, $ukField);
                        }
                    }
                }
            }

            if ($action != 'primaryKey') {
                //dd('action', $action, 'data', $data);
                //dd('action', $action, 'rules', $rules);
                //dd('action', $action, 'options', $options, 'rules', $rules, 'data', $data, 'oldData', $oldData);
            }

            $this->validator = Validator::make($data, $rules, $messages, $customAttributes);
            return $this->validator()->passes();
        }
        return true;
    }

    /**
     * Make a brand new object
     *
     * Prototype options:
     * [
     *     'model' => ['type' => 'string|Antares\Crud\CrudModel', 'default' => null],
     *     'table' => ['type' => 'string', 'default' => null],
     *     'primaryKey' => ['type' => 'string', 'default' => null],
     * ]
     *
     * @param array $options Object prototype options
     * @return static
     */
    public static function make(array $options = [])
    {
        $opt = Options::make($options, [
            'model' => ['type' => 'string|Antares\Crud\CrudModel', 'default' => null],
            'table' => ['type' => 'string', 'default' => null],
            'primaryKey' => ['type' => 'string', 'default' => null],
        ])->validate();

        if ($opt->model and is_string($opt->model)) {
            $opt->model = new $opt->model();
        }

        $r = new static;

        if ($opt->has('model')) {
            $r->table = $opt->model->getTable();
            $r->primaryKey = $opt->model->getKeyName();
        }
        if ($opt->has('table')) {
            $r->table = $opt->table;
        }
        if ($opt->has('primaryKey')) {
            $r->primaryKey = $opt->primaryKey;
        }

        return $r;
    }
}
