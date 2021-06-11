<?php

namespace Antares\Crud;

use Antares\Crud\Metadata\Field\Field;
use Antares\Crud\Metadata\Field\FieldProperties;
use Antares\Crud\Metadata\Field\GridFieldProperties;
use Antares\Crud\Metadata\Filter\Filter;
use Antares\Crud\Metadata\Layout\AbstractLayout;
use Antares\Crud\Metadata\Order\Order;
use Antares\Support\Options;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
            'primaryKey' => !empty($this->primaryKey) ? $this->primaryKey : null,
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
            'getLayout' => ['type' => 'boolean', 'default' => true],
        ])->validate();

        if ($opt->reset) {
            $this->metadata = null;
        }

        if (empty($this->metadata)) {
            $this->metadata = $this->defaultMetadata();

            $this->metadata['fields'] = ($opt->getFields === true) ? $this->getFieldsMetadata() : null;
            $this->metadata['orders'] = ($opt->getOrders === true) ? $this->getOrdersMetadata() : null;
            $this->metadata['filters'] = ($opt->getFilters === true) ? $this->getFiltersMetadata($opt->filtersOptions) : null;
            $this->metadata['grid'] = ($opt->getGrid === true) ? $this->getGridMetadata($opt->gridOptions) : null;
            $this->metadata['layout'] = ($opt->getLayout === true) ? $this->getLayoutMetadata() : null;

            if (
                $opt->getOrders === true and
                $this->metadata['orders'] == null and
                $this->getPropertiesListSource('ordersMetadata') === false and
                !empty($this->primaryKey)
            ) {
                $this->metadata['orders'] = [Order::make(['field' => $this->primaryKey, 'type' => 'asc'])];
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
     * @param string|array $sourceNameOrData
     * @param string $propertiesClass
     * @param string $uniqueProperty
     * @return null|array
     */
    public function getPropertiesListFromSource($sourceNameOrData, $propertiesClass, string $uniqueProperty = '')
    {
        if (!is_string($sourceNameOrData) and !is_array($sourceNameOrData)) {
            throw CrudException::forInvalidObjectType('string | array', $sourceNameOrData);
        }
        if (!is_null($propertiesClass) and !is_string($propertiesClass)) {
            throw CrudException::forInvalidObjectType('string | null', $propertiesClass);
        }

        $uniqueProperty = trim($uniqueProperty);
        $uniqueList = [];

        $list = null;

        $source = is_array($sourceNameOrData) ? $sourceNameOrData : $this->getPropertiesListSource($sourceNameOrData);
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
     * Get name list from metadata.fields property
     *
     * @return array
     */
    public function getFieldsMetadataNames()
    {
        $metadata = $this->metadata();
        $fieldNames = [];

        if (!empty($metadata['fields'])) {
            foreach ($$metadata['fields'] as $field) {
                $fieldNames[] = $field['name'];
            }
        }

        return $fieldNames;
    }

    /**
     * Get fields metadata
     *
     * @return array
     */
    public function getFieldsMetadata()
    {
        return $this->getPropertiesListFromSource('fieldsMetadata', Field::class, 'name');
    }

    /**
     * Get orders metadata
     *
     * @return array
     */
    public function getOrdersMetadata()
    {
        return $this->getPropertiesListFromSource('ordersMetadata', Order::class, 'field');
    }

    /**
     * Get filters metadata
     *
     * @param array $options
     * @return array
     */
    public function getFiltersMetadata(array $options = [])
    {
        $opt = Options::make($options, [
            'getStatic' => ['type' => 'boolean', 'default' => true],
            'getCustom' => ['type' => 'boolean', 'default' => true],
            'getFields' => ['type' => 'boolean', 'default' => true],
            'getLayout' => ['type' => 'boolean', 'default' => true],
        ])->validate();

        $filters = [
            'static' => ($opt->getStatic === true) ? $this->getPropertiesListFromSource('filtersStaticMetadata', Filter::class) : null,
            'custom' => ($opt->getCustom === true) ? $this->getPropertiesListFromSource('filtersCustomMetadata', Filter::class) : null,
            'fields' => ($opt->getFields === true) ? $this->getPropertiesListFromSource('filtersFieldsMetadata', FieldProperties::class) : null,
            'layout' => ($opt->getLayout === true) ? $this->getFiltersLayoutMetadata() : null,
        ];

        return $filters;
    }

    /**
     * Get filters layout metadata
     *
     * @return array
     */
    public function getFiltersLayoutMetadata()
    {
        return $this->getPropertiesListFromSource('filtersLayoutMetadata', AbstractLayout::class);
    }

    /**
     * Get grid metadata
     *
     * @param array $options
     * @return array
     */
    public function getGridMetadata(array $options = [])
    {
        $opt = Options::make($options, [
            'getFields' => ['type' => 'boolean', 'default' => true],
        ])->validate();

        $grid = [
            'fields' => ($opt->getFields === true) ? $this->getPropertiesListFromSource('gridFieldsMetadata', GridFieldProperties::class) : null,
        ];

        return $grid;
    }

    /**
     * Get layout metadata
     *
     * @return array
     */
    public function getLayoutMetadata()
    {
        return $this->getPropertiesListFromSource('layoutMetadata', AbstractLayout::class);
    }

    /**
     * Get picklists from fields
     *
     * @param array $picklists
     * @param array $fields
     * @return void
     */
    public function getPicklistsFromFields(array &$picklists, $fields)
    {
        if (!empty($fields)) {
            foreach ($fields as $field) {
                if ($field->dataSource and $field->dataSource->type == 'picklist') {
                    $picklist = $field->dataSource->id;
                    if (!in_array($picklist, $picklists)) {
                        $picklists[$picklist] = picklists($picklist);
                    }
                }
            }
        }
    }

    /**
     * Get dataSource fields
     *
     * @param array $fields
     * @return array
     */
    public function getDataSourceFields($fields = null)
    {
        $dsFields = [];

        if ($fields === null) {
            $fields = $this->getFieldsMetadata();
        }

        if (!empty($fields)) {
            foreach ($fields as $field) {
                if ($field->dataSource) {
                    $dsFields[] = $field;
                }
            }
        }

        return $dsFields;
    }

    /**
     * Convert relation fields to objects
     *
     * @param array $data
     * @return void
     */
    public function relationsToObjects(&$data)
    {
        if (!empty($data)) {
            $fields = $this->getDataSourceFields();
            $selectFields = [];

            foreach ($data as &$item) {
                foreach ($fields as $field) {
                    if (isset($item[$field['name']]) and $item[$field['name']] !== null) {
                        if (!is_object($item[$field['name']]) and !is_array($item[$field['name']])) {
                            if ($field['dataSource']['type'] == 'picklist') {
                                $item[$field['name']] = picklists($field['dataSource']['id'])->getItem($item[$field['name']]);
                            }
                            if ($field['dataSource']['type'] == 'table') {
                                if (empty($selectFields[$field['name']])) {
                                    $selectFields[$field['name']] = [$field['dataSource']['sourceKey']];
                                    foreach ($field['dataSource']['showFields'] as $key => $value) {
                                        $selectFields[$field['name']][] = $key;
                                    }
                                }
                                $query = DB::table($field['dataSource']['id'])->where($field['dataSource']['sourceKey'], $item[$field['name']]);
                                $query->select($selectFields[$field['name']]);
                                $query->where($field['dataSource']['sourceKey'], $item[$field['name']]);
                                $item[$field['name']] = $query->get()->first();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Convert relation fields to objects
     *
     * @param array $data
     * @return void
     */
    public function relationsFromObjects(&$data)
    {
        if (!empty($data)) {
            $fields = $this->getDataSourceFields();

            foreach ($data as &$item) {
                foreach ($fields as $field) {
                    if (isset($item[$field['name']]) and $item[$field['name']] !== null) {
                        if (is_object($item[$field['name']]) or is_array($item[$field['name']])) {
                            if ($field['dataSource']['type'] == 'picklist') {
                                $item[$field['name']] = $item[$field['name']]['key'];
                            }
                            if ($field['dataSource']['type'] == 'table') {
                                $item[$field['name']] = $item[$field['name']][$field['dataSource']['sourceKey']];
                            }
                        }
                    }
                }
            }
        }
    }
}
