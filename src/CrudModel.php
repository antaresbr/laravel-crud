<?php

namespace Antares\Crud;

use Antares\Crud\Metadata\Field;
use Antares\Crud\Metadata\FieldProperties;
use Antares\Crud\Metadata\GridFieldProperties;
use Antares\Crud\Metadata\Order;
use Antares\Support\Options;
use Illuminate\Database\Eloquent\Model;

class CrudModel extends Model
{
    /**
     * Get default model metadata
     *
     * @return array
     */
    public function defaultMetadata()
    {
        return [
            'table' => $this->table,
            'filters' => [
                'static' => null,
                'custom' => null,
                'fields' => null,
            ],
            'orders' => null,
            'pagination' => [
                'perPage' => config('crud.model.metadata.pagination.perPage', 30),
            ],
            'grid' => null,
            'fields' => null,
            'picklists' => null,
            'rules' => null,
        ];
    }

    /**
     * Model metadata
     *
     * @var array
     */
    protected $metadata;

    /**
     * Metadata property getter
     *
     * @param array $options
     * @return array
     */
    public function &metadata(array $options = [])
    {
        $opt = Options::make($options, [
            'reset' => ['type' => 'boolean', 'default' => false],
            'getFields' => ['type' => 'boolean', 'default' => true],
            'getOrders' => ['type' => 'boolean', 'default' => true],
            'getFilters' => ['type' => 'boolean', 'default' => true],
            'filtersOptions' => ['type' => 'array', 'default' => []],
            'getGrid' => ['type' => 'boolean', 'default' => true],
            'gridOptions' => ['type' => 'array', 'default' => []],
        ]);

        if ($opt->reset) {
            $this->metadata = null;
        }

        if (empty($this->metadata)) {
            $this->metadata = $this->defaultMetadata();

            $this->metadata['fields'] = ($opt->getFields === true) ? $this->getPropertiesListFromSource('fieldsMetadata', Field::class, 'name') : null;
            $this->metadata['orders'] = ($opt->getOrders === true) ? $this->getPropertiesListFromSource('ordersMetadata', Order::class, 'field') : null;
            $this->metadata['filters'] = ($opt->getFilters === true) ? $this->filtersMetadata($opt->filtersOptions) : null;
            $this->metadata['grid'] = ($opt->getGrid === true) ? $this->gridMetadata($opt->gridOptions) : null;

            if ($opt->getOrders === true and $this->metadata['orders'] == null and $this->getPropertiesListSource('ordersMetadata') === false and !empty($this->primaryKey)) {
                $this->metadata['orders'] = Order::make(['field' => $this->primaryKey, 'type' => 'asc']);
            }
        }

        return $this->metadata;
    }

    /**
     * Get properties list source
     *
     * @param string $sourceName
     * @return mixed
     */
    private function getPropertiesListSource(string $sourceName)
    {
        if (method_exists($this, $sourceName)) {
            return $this->{$sourceName}();
        } elseif (property_exists($this, $sourceName)) {
            return $this->{$sourceName};
        }

        return false;
    }

    /**
     * Get properties list from source
     *
     * @param string $sourceName
     * @param string $propertiesClass
     * @param string $uniqueProperty
     * @return null|array
     */
    private function getPropertiesListFromSource(string $sourceName, $propertiesClass, string $uniqueProperty = '')
    {
        if (!is_null($propertiesClass) and !is_string($propertiesClass)) {
            throw CrudException::forInvalidObjectType('string | null', $propertiesClass);
        }

        $uniqueProperty = trim($uniqueProperty);
        $uniqueList = [];

        $list = null;

        $source = $this->getPropertiesListSource($sourceName);
        if ($source !== false) {
            if (is_string($source)) {
                $source = explode('|', $source);
            }
            if (!is_array($source)) {
                throw CrudException::forInvalidObjectType('string | array', $source);
            }
            $list = [];
            foreach ($source as $key => $item) {
                if (is_string($item)) {
                    $key = trim($item);
                    $item = [];
                }
                if (!is_null($propertiesClass)) {
                    if (is_array($item)) {
                        $item = $propertiesClass::make($item);
                    }
                    if (!($item instanceof $propertiesClass)) {
                        throw CrudException::forInvalidObjectType($propertiesClass, $item);
                    }
                }
                if ($uniqueProperty != '') {
                    if (!is_null($propertiesClass)) {
                        $uniqueValue = $item->{$uniqueProperty};
                    } else {
                        $uniqueValue = array_key_exists($uniqueProperty, $item) ? $item[$uniqueProperty] : null;
                    }
                    if (!is_null($uniqueValue)) {
                        if (in_array($uniqueValue, $uniqueList)) {
                            throw CrudException::forAlreadyDefinedItem($uniqueValue);
                        } else {
                            $uniqueList[] = $uniqueValue;
                        }
                    }
                }
                $list[$key] = $item;
            }
        }

        return $list;
    }

    /**
     * Get filters metadata
     *
     * @param array $options
     * @return array
     */
    public function filtersMetadata(array $options = [])
    {
        $opt = Options::make($options, [
            'getStatic' => ['type' => 'boolean', 'default' => false],
            'getCustom' => ['type' => 'boolean', 'default' => false],
            'getFields' => ['type' => 'boolean', 'default' => true],
        ]);

        $filters = [
            'static' => ($opt->getStatic === true) ? $this->getPropertiesListFromSource('filtersStaticMetadata', null) : null,
            'custom' => ($opt->getCustom === true) ? $this->getPropertiesListFromSource('filtersCustomMetadata', null) : null,
            'fields' => ($opt->getFields === true) ? $this->getPropertiesListFromSource('filtersFieldsMetadata', FieldProperties::class) : null,
        ];

        return $filters;
    }

    /**
     * Get grid metadata
     *
     * @param array $options
     * @return array
     */
    public function gridMetadata(array $options = [])
    {
        $opt = Options::make($options, [
            'getFields' => ['type' => 'boolean', 'default' => true],
        ]);

        $grid = [
            'fields' => ($opt->getFields === true) ? $this->getPropertiesListFromSource('gridFieldsMetadata', GridFieldProperties::class) : null,
        ];

        return $grid;
    }
}
