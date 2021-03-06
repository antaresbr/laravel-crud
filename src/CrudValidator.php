<?php

namespace Antares\Crud;

use Antares\Crud\Ruler\CrudRuler;
use Antares\Support\Options;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class CrudValidator
{
    /**
     * Protected property primaryKey accessor
     *
     * @return array
     */
    public function primaryKey()
    {
        if (property_exists($this, 'primaryKey') and !empty($this->primaryKey['keyName'])) {
            return $this->primaryKey;
        }

        return [];
    }

    /**
     * Protected property primaryKey accessor
     *
     * @return array
     */
    public function defaulRules()
    {
        if (property_exists($this, 'defaulRules') and !empty($this->defaulRules)) {
            return $this->defaulRules;
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
    public function getRules(string $action = '', $pkOptions = [])
    {
        $rules = [];

        $action = trim($action);
        if ($action != '') {
            $method = 'get' . str_replace(' ', '', ucwords(strtolower($action))) . 'Rules';
            if (method_exists($this, $method)) {
                $rules = method_exists($this, $method) ? $this->$method($pkOptions) : [];
            }
        }

        return $rules;
    }

    /**
     * Get the validation rules for supplied action as metadata
     *
     * @param string $action
     * @param string $pkOptions
     * @return array
     */
    public function getRulesAsMetadata(string $action = '', $pkOptions = [])
    {
        $source = $this->getRules($action, $pkOptions);

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
     * Build the primary key rules base on the array property 'primaryKey', with the attributes as follow
     *
     * Options parameter as in \Antares\Crud\Ruler\CrudRuler::getRules()
     *
     * @param bool $pkOptions
     * @return array
     */
    public function getPrimaryKeyRules(array $pkOptions = [])
    {
        $rules = [];
        if (!property_exists($this, 'primaryKey') or empty($this->primaryKey['keyName'])) {
            return $rules;
        }

        return CrudRuler::make($this->primaryKey)->getRules($pkOptions);
    }

    public function getIndexRules(array $pkOptions = [])
    {
        return [];
    }

    public function getStoreRules(array $pkOptions = [])
    {
        return array_merge($this->getPrimaryKeyRules($pkOptions), $this->defaulRules());
    }

    public function getShowRules(array $pkOptions = [])
    {
        return $this->getPrimaryKeyRules($pkOptions);
    }

    public function getUpdateRules(array $pkOptions = [])
    {
        return $this->getPrimaryKeyRules($pkOptions) + $this->defaulRules();
    }

    public function getDestroyRules(array $pkOptions = [])
    {
        return $this->getPrimaryKeyRules($pkOptions);
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
     * @param array $pkOptions
     * @param array $messages
     * @param array $customAttributes
     * @return boolean
     */
    public function validate($action, $data, $pkOptions = [], $messages = [], $customAttributes = [])
    {
        $this->validator = null;
        $rules = $this->getRules($action, $pkOptions);
        if (!empty($rules)) {
            $this->validator = Validator::make($data, $rules, $messages, $customAttributes);
            return $this->validator()->passes();
        }
        return true;
    }
}
