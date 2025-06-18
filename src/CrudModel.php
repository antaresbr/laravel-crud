<?php

namespace Antares\Crud;

use Antares\Crud\Metadata\Detail\Detail;
use Antares\Crud\Metadata\Field\Field;
use Antares\Crud\Metadata\Field\FieldProperties;
use Antares\Crud\Metadata\Field\GridFieldProperties;
use Antares\Crud\Metadata\Filter\Filter;
use Antares\Crud\Metadata\Layout\AbstractLayout;
use Antares\Crud\Metadata\Menu;
use Antares\Crud\Metadata\Order\Order;
use Antares\Foundation\Arr;
use Antares\Foundation\Options\Options;
use finfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CrudModel extends Model
{
    /**
     * Get api property, if any
     *
     * @return string
     */
    public function getApi()
    {
        return property_exists($this, 'api') ? $this->api : $this->getTable();
    }

    /**
     * Get default perPage value
     *
     * @return array
     */
    public function defaultPerPage()
    {
        return config('crud.model.metadata.pagination.perPage', 30);
    }

    /**
     * Get default model metadata
     *
     * @return array
     */
    public function defaultMetadata()
    {
        return [
            'api' => $this->getApi(),
            'table' => $this->table,
            'primaryKey' => !empty($this->primaryKey) ? $this->primaryKey : null,
            'filters' => [
                'ignoreStatic' => false,
                'static' => null,
                'custom' => null,
                'fields' => null,
            ],
            'orders' => null,
            'pagination' => [
                'perPage' => $this->defaultPerPage(),
            ],
            'grid' => null,
            'layout' => null,
            'menu' => null,
            'fields' => null,
            'picklists' => null,
            'rules' => null,
            'details' => null,
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
            'getMenu' => ['type' => 'boolean', 'default' => true],
            'getPicklists' => ['type' => 'boolean', 'default' => true],
            'getDetails' => ['type' => 'boolean', 'default' => true],
        ])->validate();

        if ($opt->reset) {
            $this->metadata = null;
        }

        if (empty($this->metadata)) {
            $defaultMetadata = $this->defaultMetadata(); 
            $this->metadata = $defaultMetadata;

            $this->metadata['fields'] = ($opt->getFields === true) ? $this->getFieldsMetadata() : null;
            $this->metadata['orders'] = ($opt->getOrders === true) ? $this->getOrdersMetadata() : null;
            $this->metadata['filters'] = ($opt->getFilters === true) ? $this->getFiltersMetadata($opt->filtersOptions) : null;
            Arr::set($this->metadata, 'filters.ignoreStatic', Arr::get($defaultMetadata, 'filters.ignoreStatic', false));
            $this->metadata['grid'] = ($opt->getGrid === true) ? $this->getGridMetadata($opt->gridOptions) : null;
            $this->metadata['layout'] = ($opt->getLayout === true) ? $this->getLayoutMetadata() : null;
            $this->metadata['menu'] = ($opt->getMenu === true) ? $this->getMenuMetadata() : null;
            $this->metadata['picklists'] = ($opt->getPicklists === true) ? $this->getPicklistsMetadata() : null;
            $this->metadata['details'] = ($opt->getDetails === true) ? $this->getDetailsMetadata() : null;
    
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
     * @param bool $asAssoc Flag to return metadata as associative array
     * @return array
     */
    public function getFieldsMetadata(bool $asAssoc = false)
    {
        $metadata = $this->getPropertiesListFromSource('fieldsMetadata', Field::class, 'name');

        if ($asAssoc === true) {
            $assoc = [];
            foreach ($metadata as $field) {
                $assoc[$field['name']] = $field;
            }
            return $assoc;
        }

        return $metadata;
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
            'getTemplate' => ['type' => 'boolean', 'default' => true],
            'getFields' => ['type' => 'boolean', 'default' => true],
            'getLayout' => ['type' => 'boolean', 'default' => true],
        ])->validate();

        $filters = [
            'static' => ($opt->getStatic === true) ? $this->getPropertiesListFromSource('filtersStaticMetadata', Filter::class) : null,
            'custom' => ($opt->getCustom === true) ? $this->getPropertiesListFromSource('filtersCustomMetadata', Filter::class) : null,
            'template' => ($opt->getCustom === true) ? $this->getPropertiesListFromSource('filtersTemplateMetadata', Filter::class) : null,
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
     * Get menu metadata
     *
     * @return array
     */
    public function getMenuMetadata()
    {
        $meta = $this->getPropertiesListSource('menuMetadata');

        if (!empty($meta['items'])) {
            $meta['items'] = $this->getPropertiesListFromSource($meta['items'], Menu::class);
        }

        return $meta;
    }

    /**
     * Get picklists metadata
     *
     * @return array
     */
    public function getPicklistsMetadata()
    {
        $picklists = [];

        if (!empty($this->metadata['fields'])) {
            $this->getPicklistsFromFields($picklists, $this->metadata['fields']);
        }
        if (!empty($this->metadata['filters']['fields'])) {
            $this->getPicklistsFromFields($picklists, $this->metadata['filters']['fields']);
        }

        return $picklists;
    }

    /**
     * Get details metadata
     *
     * @return array
     */
    public function getDetailsMetadata()
    {
        return $this->getPropertiesListFromSource('detailsMetadata', Detail::class);
    }

    /**
     * Get model metadata as a data source
     *
     * @return array
     */
    public function asDataSourceMetadata() {
        return property_exists($this, 'asDataSourceMetadata') ? $this->asDataSourceMetadata : [];
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
    public function getDataSourceFields($fields = null, $selectFields = null)
    {
        $dsFields = [];

        if ($fields === null) {
            $fields = $this->getFieldsMetadata();
        }

        if (!empty($fields)) {
            foreach ($fields as $field) {
                if ($field->dataSource) {
                    if ($selectFields == null or array_key_exists($field->name, $selectFields)) {
                        $dsFields[] = $field;
                    }
                }
            }
        }

        return $dsFields;
    }

    /**
     * Get datasource fields relations
     *
     * @param array $fields
     * @param array $selectFields
     * @return array
     */
    public function getDatasourceFieldsRelations($fields = null, $selectFields = null)
    {
        $relations = [];

        $dsFields = $this->getDataSourceFields($fields, $selectFields);
        if (empty($dsFields)) {
            return $relations;
        }

        foreach($dsFields as $field) {
            $fieldName = $field['name'];
            if (isset($relations[$fieldName])) {
                continue;
            }
            $relation = [
                'type' => $field['dataSource']['type'],
                'id' => $field['dataSource']['id'],
            ];
            if ($field['dataSource']['type'] == 'table') {
                $relation['relations'] = [];
            }
            if ($field['dataSource']['type'] == 'table') {
                $relation['sourceKey'] = $field['dataSource']['sourceKey'];
                $relation['selectFields'] = [$field['dataSource']['sourceKey'] => []];
                if ($selectFields != null) {
                    foreach ($selectFields[$fieldName] as $key => $value) {
                        $relation['selectFields'] = Arr::add($relation['selectFields'], $key, []);
                    }
                }
                else {
                    foreach ($field['dataSource']['showFields'] as $key => $value) {
                        $relation['selectFields'] = Arr::add($relation['selectFields'], $key, []);
                    }
                    foreach ($field['dataSource']['optionFields'] as $key => $value) {
                        $relation['selectFields'] = Arr::add($relation['selectFields'], $key, []);
                    }
                }
                if (!empty($field['dataSource']['metadata']['fields'])) {
                    $relation['relations'] = $this->getDatasourceFieldsRelations($field['dataSource']['metadata']['fields'], $relation['selectFields']);
                }
            }
            $relations[$fieldName] = $relation;
        }

        return $relations;
    }

    /**
     * Item object relations
     *
     * @param array|stcClass|Model $item
     * @param array $relations
     * @return void
     */
    private function relationsToObjects_item(&$item, $relations)
    {
        if ($item and is_array($item)) {
            $item = (object) $item;
        }
        foreach ($relations as $fieldName => $relation) {
            if (isset($item->{$fieldName}) and $item->{$fieldName} !== null) {
                if (!is_object($item->{$fieldName}) and !is_array($item->{$fieldName})) {
                    if ($relation['type'] == 'picklist') {
                        $item->{$fieldName} = picklists($relation['id'])->getItem($item->{$fieldName});
                    }
                    if ($relation['type'] == 'table') {
                        $query = DB::table($relation['id'])->where($relation['sourceKey'], $item->{$fieldName});
                        $query->select(array_keys($relation['selectFields']));
                        $query->where($relation['sourceKey'], $item->{$fieldName});
                        
                        $item->{$fieldName} = $query->get()->first();
                        
                        if ($item->{$fieldName} and !empty($relation['relations'])) {
                            $this->relationsToObjects_item($item->{$fieldName}, $relation['relations']);
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
     * @param array $metadata
     * @return void
     */
    public function relationsToObjects(&$data)
    {
        $relations = [];
        if (!empty($data)) {
            $relations = $this->getDatasourceFieldsRelations();
        }
        if (empty($data) or empty($relations)) {
            return;
        }

        foreach ($data as &$item) {
            $this->relationsToObjects_item($item, $relations);
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

    /**
     * Apply fieldsMetadata properties on data
     *
     * @param  array $data
     * @return void
     */
    public function applyFieldsMetadataPropertiesOnData(array &$data)
    {
        if (empty($data)) {
            return;
        }

        $metadata = $this->getFieldsMetadata(true);

        foreach($data as &$item) {
            foreach(array_keys($item) as $key) {
                if (array_key_exists($key, $metadata)) {
                    //-- letterCase
                    if (!empty($metadata[$key]['letterCase'])) {
                        switch ($metadata[$key]['letterCase']) {
                            case 'upper':
                                $item[$key] = mb_strtoupper($item[$key]);
                                break;
                            case 'lower':
                                $item[$key] = mb_strtolower($item[$key]);
                                break;
                            case 'sentence':
                                $item[$key] = mb_strtoupper(mb_substr($item[$key], 0, 1)) . mb_strtolower(mb_substr($item[$key], 1));
                                break;
                            case 'capitalized':
                                $item[$key] = mb_convert_case($item[$key], MB_CASE_TITLE);
                                break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Calculate virtual fields
     *
     * @param  array $data
     * @param  array $metadata
     * @return void
     */
    public function calculateVirtualFields(array &$data, $metadata = null)
    {
        //-- to be overrided in descendant classes to calculate virtual fields
    }

    /**
     * Prepare data to send
     *
     * @param  array $data
     * @param  array $metadata
     * @return void
     */
    public function prepareDataToSend(array &$data, $metadata = null)
    {
        if (empty($data)) {
            return;
        }

        if (!is_array($metadata)) {
            $metadata = $this->getFieldsMetadata(true);
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        foreach($data as &$item) {
            if ($item instanceof static) {
                $keys = array_keys($item->getAttributes());
            } elseif (is_array($item)) {
                $keys = array_keys($item);
            } else {
                $keys = [];
            }
            foreach($keys as $key) {
                if (array_key_exists($key, $metadata)) {
                    //-- blob
                    if ($metadata[$key]['type'] == 'blob') {
                        $b64 = base64_encode($item[$key]);
                        $item[$key] = empty($b64) ? null : 'data:' . $finfo->buffer($item[$key]) .';base64,'. $b64;
                    }
                }
            }
        }

        $this->calculateVirtualFields($data, $metadata);
    }

    /**
     * Prepare data to save
     *
     * @param  array $data
     * @param  array $metadata
     * @return void
     */
    public function prepareDataToSave(array &$data, $metadata = null)
    {
        if (empty($data)) {
            return;
        }

        if (!is_array($metadata)) {
            $metadata = $this->getFieldsMetadata(true);
        }

        foreach($data as &$item) {
            if ($item instanceof static) {
                $keys = array_keys($item->getAttributes());
            } elseif (is_array($item)) {
                $keys = array_keys($item);
            } else {
                $keys = [];
            }
            foreach($keys as $key) {
                if (array_key_exists($key, $metadata)) {
                    //-- blob
                    if ($metadata[$key]['type'] == 'blob') {
                        $pieces = explode(';', $item[$key]);
                        $blob = end($pieces);
                        if (str_starts_with($blob, 'base64')) {
                            $blob = substr($blob, 6);
                        }
                        if (str_starts_with($blob, ':')) {
                            $blob = substr($blob, 1);
                        }
                        if (str_starts_with($blob, ',')) {
                            $blob = substr($blob, 1);
                        }
                        $item[$key] = empty($blob) ? null : base64_decode($blob);
                    }
                }
            }
        }
    }

    /**
     * Translate the field names in error messages
     *
     * @param  array $data
     * @return array
     */
    public function translateFieldnamesInErrors($data)
    {
        if (empty($data)) {
            return $data;
        }

        $metadata = $this->getFieldsMetadata(true);

        $translated = [];
        foreach($data as $fieldName => $errors) {
            if (!empty($metadata[$fieldName]['label'])) {
                $label = $metadata[$fieldName]['label'];
                foreach($errors as &$error) {
                    $error = str_replace($fieldName, $label . ' ['. $fieldName . ']', $error);
                }
            }
            $translated[$fieldName] = $errors;
        }

        return $translated;
    }
}
