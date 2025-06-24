<?php
namespace Antares\Crud;

use Antares\Crud\Http\CrudHttpErrors;
use Antares\Crud\Http\CrudJsonResponse;
use Antares\Crud\Metadata\DataSource\TableDataSource;
use Antares\Crud\Metadata\Filter\Filter;
use Antares\Crud\Metadata\Order\Order;
use Antares\Foundation\Arr;
use Antares\Foundation\Options\Options;
use Antares\Foundation\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Facades\DB;

abstract class CrudHandler
{
    /**
     * Request used in this handler
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Acessor for request property
     *
     * @return \Illuminate\Http\Request
     */
    public function request()
    {
        if (empty($this->request)) {
            $this->request = app()->request;
        }

        return $this->request;
    }

    /**
     * CrudModel used in this handler
     *
     * @var \Antares\Crud\CrudModel
     */
    protected $model;

    /**
     * Errors bag
     *
     * @var \Antares\Crud\CrudMessageBag
     */
    private $errors;

    /**
     * Errors bag access
     *
     * @return \Antares\Crud\CrudMessageBag
     */
    public function errors()
    {
        if (!$this->errors) {
            $this->errors = new CrudMessageBag();
        }
        return $this->errors;
    }

    /**
     * Return a JsonResponse with error based on errors bag or true if no error in errors bag
     *
     * @param array $options
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function errorResponseOrTrue(array $options = [])
    {
        if ($this->errors()->isEmpty()) {
            return true;
        }

        $opt = Options::make($options, [
            'error' => ['type' => 'array', 'nullable' => true],
            'code' => ['type' => 'integer', 'nullable' => true],
            'message' => ['type' => 'string', 'nullable' => true],
            'action' => ['type' => 'string', 'nullable' => true],
            'parent_action' => ['type' => 'string', 'nullable' => true],
            'source' => ['type' => 'mixed', 'nullable' => true],
            'data' => ['type' => 'array', 'nullable' => false, 'default' => []],
            'httpStatus' => ['type' => 'integer', 'nullable' => true],
            'clearAfterUse' => ['type' => 'boolean', 'default' => true],
        ])->validate();

        if ($opt->has('error') and !$opt->has('code')) {
            $opt->code = $opt->error['code'];
        }
        if ($opt->has('error') and !$opt->has('message')) {
            $opt->message = $opt->error['message'];
        }

        $data = $opt->data;
        if ($opt->has('action')) {
            $data['action'] = $opt->action;
        }
        if ($opt->has('parent_action')) {
            $data['parent_action'] = $opt->parent_action;
        }
        if ($opt->has('source')) {
            $data['source'] = $opt->source;
        }
        $data['errors'] = $this->errors()->get('*');

        if ($opt->clearAfterUse === true) {
            $this->errors()->clear();
        }

        return CrudJsonResponse::error(
            $opt->code,
            $opt->message,
            $data,
            $opt->httpStatus
        );
    }

    /**
     * Crud validador for this handler
     *
     * @var CrudValidator
     */
    protected $validator;

    /**
     * Validate some data for the supplied action
     *
     * @param array $data
     * @param array $oldData
     * @param string $action
     * @param string $parentAction
     * @param array $options
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function validateData($data, $oldData, $action, $parentAction = null, $options = [])
    {
        if ($this->validator) {
            if (!$this->validator->validate($action, $data, $oldData, $options)) {
                return CrudJsonResponse::error(CrudHttpErrors::DATA_VALIDATION_ERROR, null, [
                    'action' => $action,
                    'parent_action' => $parentAction,
                    'errors' => $this->model->translateFieldnamesInErrors($this->validator->errors()),
                    'source' => $data,
                ]);
            }
        }

        return true;
    }

    /**
     * Extract model attributes from source array
     *
     * @param array $source
     * @param array $attributes
     * @return array
     */
    public function attributesFromData(array $source, array $attributes = [])
    {
        if (empty($attributes)) {
            $attributes = $this->model->getAttributes();
        }
        if (!empty($attributes) and !in_array('uuid', $attributes)) {
            $attributes[] = 'uuid';
        }

        if (!empty($source) or empty($attributes)) {
            return $source;
        }

        $data = [];

        foreach ($attributes as $attribute) {
            if (array_key_exists($attribute, $source)) {
                $data[$attribute] = $source[$attribute];
            }
        }

        return $data;
    }

    /**
     * Extract model attributes from current request
     *
     * @param string $dataPrefix
     * @param array $attributes
     * @return array
     */
    public function attributesFromRequest(string $dataPrefix = '', array $attributes = [])
    {
        $dataPrefix = empty($dataPrefix) ? 'data' : Str::start($dataPrefix, 'data.');

        $source = $this->request()->has($dataPrefix) ? $this->request()->input($dataPrefix) : [];

        return $this->attributesFromData($source, $attributes);
    }

    /**
     * Get model from request primary key
     *
     * @param string $action
     * @param string $parentAction
     * @return \Antares\Crud\CrudModel|\Illuminate\Http\JsonResponse
     */
    public function getModelByPrimaryKey($action = null, $keyValue = null)
    {
        $keyName = $this->model->getKeyName();

        if ($keyValue == '_') {
            if ($this->request()->has("data.primaryKey.{$keyName}")) {
                $keyValue = $this->request()->input("data.primaryKey.{$keyName}");
            } elseif ($this->request()->has("data.old.{$keyName}")) {
                $keyValue = $this->request()->input("data.old.{$keyName}");
            } elseif ($this->request()->has('data.items')) {
                $items = $this->request()->input('data.items');
                if (Arr::has($items, $keyName)) {
                    $keyValue = Arr::get($items, $keyName);
                } else {
                    $first = Arr::first($items);
                    if (Arr::has($first, $keyName)) {
                        $keyValue = Arr::get($first, $keyName);
                    }
                }
            }
        }

        $data = [$keyName => $keyValue];

        $r = $this->validateData($data, null, 'primaryKey', $action, [$keyName => ['includeUniqueRules' => false]]);
        if ($r !== true) {
            return $r;
        }

        $modelClass = get_class($this->model);
        if ($model = $modelClass::find($data[$keyName])) {
            return $model;
        }

        return CrudJsonResponse::error(CrudHttpErrors::TARGET_DATA_MODEL_NOT_FOUND, null, [
            'action' => 'primaryKey',
            'parent_action' => $action,
            'target' => $data,
        ]);
    }

    /**
     * Get crud metadata.
     *
     * @return \Illuminate\Http\Response
     */
    public function metadata(Request $request)
    {
        $data = ['action' => __FUNCTION__];

        if ($request->boolean('data.asDataSource')) {
            $data['asDataSource'] = TableDataSource::make(['model' => $this->model])->toArray();
        } else {
            $data['metadata'] = $this->model->metadata();

            $rules = [];
            if ($this->validator) {
                $rules['index'] = $this->validator->getRulesAsMetadata('index');
                $rules['store'] = $this->validator->getRulesAsMetadata('store');
                $rules['show'] = $this->validator->getRulesAsMetadata('show');
                $rules['update'] = $this->validator->getRulesAsMetadata('update');
                $rules['destroy'] = $this->validator->getRulesAsMetadata('destroy');
            }
            $data['metadata']['rules'] = $rules;
        }

        return CrudJsonResponse::successful($data);
    }

    /**
     * Crud search - index for datasources
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $request->request->add(['__search__' => true]);
        return $this->doIndex($request);
    }

    /**
     * Action executed before index action
     *
     * @param  array $metadata
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function beforeIndex(array &$metadata)
    {
        return true;
    }

    /**
     * Action executed after index action
     *
     * @param  array $items
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function afterIndex(array &$items)
    {
        return true;
    }

    /**
     * Get metadata from index request
     *
     * @param Request $request
     * @return array
     */
    protected function &indexGetMetadata(Request $request)
    {
        $metadata = &$this->model->metadata([
            'filtersOptions' => ['getTemplate' => false, 'getFields' => false, 'getLayout' => false],
            'getFields' => false,
            'getGrid' => false,
            'getLayout' => false,
            'getDetails' => false,
        ]);

        Arr::set($metadata, 'filters.ignoreStatic', $request->input('data.metadata.filters.ignoreStatic', false));

        $filters = $request->input('data.metadata.filters.custom');
        if (!empty($filters)) {
            Arr::set($metadata, 'filters.custom', $this->model->getPropertiesListFromSource($filters, Filter::class));
        }

        $orders = $request->input('data.metadata.orders');
        if (!empty($orders)) {
            Arr::set($metadata, 'orders', $this->model->getPropertiesListFromSource($orders, Order::class, 'field'));
        }

        if ($request->has('data.metadata.pagination.perPage')) {
            Arr::set($metadata, 'pagination.perPage', $request->input('data.metadata.pagination.perPage'));
        }

        if ($request->has('data.metadata.pagination.targetPage')) {
            Arr::set($metadata, 'pagination.targetPage', $request->input('data.metadata.pagination.targetPage'));
        }

        return $metadata;
    }

    /**
     * Get metadata from index request
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @param bool $wrapFields
     * @return array
     */
    protected function indexQueryFilters(Builder $query, $filters, bool $wrapFields = true)
    {
        if (!empty($filters)) {
            if ($wrapFields) {
                $filters = [
                    Filter::make([
                        'filters' => $filters,
                        'conjunction' => 'and',
                    ]),
                ];
            }

            foreach ($filters as $filter) {
                if (is_array($filter)) {
                    $filter = Filter::make($filter);
                }
                if (!($filter instanceof Filter)) {
                    throw CrudException::forInvalidObjectType(Filter::class, $filter);
                }

                if (!empty($filter->filters)) {
                    $nested = $this->model->newQueryWithoutRelationships();
                    $this->indexQueryFilters($nested, $filter->filters, false);
                    $query->addNestedWhereQuery($nested->getQuery(), $filter->conjunction);
                } else {
                    $filter->normalizeToDatabaseDriver($this->model);

                    if (Str::icIn($filter->operator, 'between', 'not between')) {
                        $query->whereBetween(
                            $filter->column,
                            [$filter->value, $filter->endValue],
                            $filter->conjunction,
                            ($filter->operator == 'not between')
                        );
                    } elseif (Str::icIn($filter->operator, 'in', 'not in')) {
                        $query->whereIn(
                            $filter->column,
                            $filter->value,
                            $filter->conjunction,
                            ($filter->operator == 'not in')
                        );
                    } elseif (Str::icIn($filter->operator, 'is null')) {
                        $query->whereNull($filter->column);
                    } elseif (Str::icIn($filter->operator, 'is not null')) {
                        $query->whereNotNull($filter->column);
                    } else {
                        $query->where(
                            $filter->column,
                            $filter->operator,
                            Str::icIn($filter->operator, 'like', 'not like', 'ilike', 'not ilike') ? Str::finish($filter->value, '%') : $filter->value,
                            $filter->conjunction
                        );
                    }
                }
            }
        }

        return $query;
    }

    /**
     * Get a resource list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->request->remove('__search__');
        return $this->doIndex($request);
    }

    /**
     * Do index action, getting a resource list.
     *
     * @return \Illuminate\Http\Response
     */
    public function doIndex(Request $request)
    {
        $metadata = &$this->indexGetMetadata($request);

        $r = $this->beforeIndex($metadata);
        if ($r !== true) {
            return $r;
        }

        $query = $this->model->query();

        //-- filters
        if ($request->input('__search__', false) === true and $request->input('data.metadata.filters.ignoreStatic', false) === true) {
            Arr::set($metadata, 'filters.static', null);
        } else {
            $this->indexQueryFilters($query, Arr::get($metadata, 'filters.static'));
        }
        $this->indexQueryFilters($query, Arr::get($metadata, 'filters.custom'));

        //-- orders
        $orders = Arr::has($metadata, 'orders') ? $metadata['orders'] : [];
        if (!empty($orders)) {
            foreach ($orders as $order) {
                if (is_array($order)) {
                    $order = Order::make($order);
                }
                if (!($order instanceof Order)) {
                    throw CrudException::forInvalidObjectType(Order::class, $order);
                }
                $query->orderBy($order->field, $order->type);
            }
        }

        if (Arr::has($metadata, 'pagination.targetPage')) {
            $request->merge(['page' => Arr::get($metadata, 'pagination.targetPage')]);
        }
        $perPage = Arr::get($metadata, 'pagination.perPage', 0);

        $resource = ($perPage > 0) ? $query->paginate($perPage) : $query->get();

        $items = ($resource instanceof AbstractPaginator) ? $resource->items() : $resource->toArray();
        $metadata['pagination'] = CrudPagination::make($resource)->toArray();

        $this->model->relationsToObjects($items);
        $this->model->prepareDataToSend($items);

        $r = $this->afterIndex($items);
        if ($r !== true) {
            return $r;
        }

        return CrudJsonResponse::successful([
            'action' => $request->input('__search__', false) ? 'search' : 'index',
            'metadata' => $metadata,
            'items' => $items,
        ]);
    }

    /**
     * Action executed before each item store action
     *
     * @param  array $data
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function beforeStore(array &$data)
    {
        return true;
    }

    /**
     * Action executed after each item store action
     *
     * @param  array $data
     * @param  \Antares\Crud\CrudModel  $model
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function afterStore(array &$data, CrudModel $model)
    {
        return true;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $items = $this->request()->has('data.items') ? $this->request()->input('data.items') : [];
        if (empty($items)) {
            return CrudJsonResponse::error(CrudHttpErrors::NO_DATA_SUPPLIED, null, [
                'action' => __FUNCTION__,
                'items' => $items,
            ]);
        }

        if (Arr::isAssoc($items)) {
            $items = [$items];
        }

        $metadata = $this->model->getFieldsMetadata(true);

        $this->model->relationsFromObjects($items);
        $this->model->applyFieldsMetadataPropertiesOnData($items);

        $modelClass = get_class($this->model);
        $keyName = $this->model->getKeyName();

        $successful = [];
        $error = [];

        foreach ($items as $item) {
            $data = $this->attributesFromData($item);

            $r = $this->beforeStore($data);
            if ($r === true) {
                $r = $this->validateData($data, null, __FUNCTION__);
            }
            if ($r !== true) {
                $error[] = $r->getData();
                continue;
            }

            $model = new $modelClass();

            $data = [$data];
            $this->model->prepareDataToSave($data, $metadata);
            $data = $data[0];

            $model->fill($data);
            if (Arr::has($data, $keyName)) {
                $model->{$keyName} = Arr::get($data, $keyName);
            }

            $afterOk = false;
            DB::beginTransaction();

            $dbOk = $model->save();

            if ($dbOk === true) {
                $afterOk = $this->afterStore($data, $model);
            }

            if ($dbOk === true and $afterOk === true) {
                DB::commit();
                $successful[] = $model;
            } else {
                DB::rollback();
                $error[] = ($afterOk instanceof \Illuminate\Http\JsonResponse)
                    ? $afterOk->getData()
                    : CrudJsonResponse::error(CrudHttpErrors::DATA_MODEL_CREATE_FAIL, null, $model)->getData()
                ;
            }
        }

        $this->model->relationsToObjects($successful);
        $this->model->prepareDataToSend($successful, $metadata);

        $resultData = [
            'action' => __FUNCTION__,
            'successful' => $successful,
            'error' => $error,
        ];

        if (!empty($successful) and empty($error)) {
            return CrudJsonResponse::successful($resultData, null, Response::HTTP_CREATED);
        }

        return CrudJsonResponse::error(empty($successful) ? CrudHttpErrors::ACTION_ERROR : CrudHttpErrors::PARTIALLY_SUCCESSFUL, null, $resultData);
    }

    /**
     * Action executed before show action
     *
     * @param  mixed $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function beforeShow($id)
    {
        return true;
    }

    /**
     * Action executed after show action
     *
     * @param  \Antares\Crud\CrudModel  $model
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function afterShow(CrudModel $model)
    {
        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $r = $this->beforeShow($id);
        if ($r !== true) {
            return $r;
        }

        $model = $this->getModelByPrimaryKey(__FUNCTION__, $id);

        if ($model instanceof \Illuminate\Http\JsonResponse) {
            return $model;
        }

        $r = $this->afterShow($model);
        if ($r !== true) {
            return $r;
        }

        $items = [$model];
        $this->model->relationsToObjects($items);
        $this->model->prepareDataToSend($items);

        return CrudJsonResponse::successful([
            'action' => __FUNCTION__,
            'items' => $items,
        ]);
    }

    /**
     * Action executed before each item update action
     *
     * @param  array $old
     * @param  array $delta
     * @param  \Antares\Crud\CrudModel  $model
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function beforeUpdate(array &$old, array &$delta, CrudModel $model)
    {
        return true;
    }

    /**
     * Action executed after each item update action
     *
     * @param  array $data
     * @param  \Antares\Crud\CrudModel  $model
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function afterUpdate(array &$data, CrudModel $model)
    {
        return true;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $delta = $this->request()->has('data.delta') ? $this->request()->input('data.delta') : [];
        if (empty($delta)) {
            return CrudJsonResponse::error(CrudHttpErrors::NO_DATA_SUPPLIED, null, [
                'action' => __FUNCTION__,
                'delta' => $delta,
            ]);
        }
        if (Arr::isAssoc($delta)) {
            $delta = [$delta];
        }

        $old = $this->request()->has('data.old') ? $this->request()->input('data.old') : [];
        if (Arr::isAssoc($old)) {
            $old = [$old];
        }

        if (count($delta) != count($old)) {
            return CrudJsonResponse::error(CrudHttpErrors::ARRAY_LENGTHS_DIFFER, null, [
                'action' => __FUNCTION__,
                'delta' => $delta,
                'old' => $old,
            ]);
        }

        $metadata = $this->model->getFieldsMetadata(true);

        $this->model->relationsFromObjects($old);
        $this->model->relationsFromObjects($delta);
        $this->model->applyFieldsMetadataPropertiesOnData($delta);

        $items = [];
        for ($i = 0; $i < count($delta); $i++) {
            $items[$i] = [
                'delta' => $delta[$i],
                'old' => $old[$i],
            ];
        }
        $delta = null;
        $old = null;

        $keyName = $this->model->getKeyName();

        $successful = [];
        $error = [];

        foreach ($items as $item) {
            $old = $this->attributesFromData($item['old']);
            $delta = $this->attributesFromData($item['delta']);

            $realDelta = [];
            foreach ($delta as $key => $value) {
                if (array_key_exists($key, $metadata)) {
                    //-- virtual
                    if (Arr::get($metadata, "{$key}.virtual", false) === true) {
                        continue;
                    }
                }
                if ($value !== $old[$key]) {
                    $realDelta[$key] = $value;
                }
            }
            $delta = $realDelta;

            if (!Arr::has($old, $keyName)) {
                $error[] = CrudJsonResponse::error(CrudHttpErrors::NO_PRIMARY_KEY_SUPPLIED, null, $old)->getData();
                continue;
            }

            $model = $this->getModelByPrimaryKey(__FUNCTION__, $old[$keyName]);
            if ($model instanceof \Illuminate\Http\JsonResponse) {
                $error[] = $model->getData();
                continue;
            }

            $r = $this->beforeUpdate($old, $delta, $model);
            if ($r !== true) {
                $error[] = $r->getData();
                continue;
            }

            $dirty = [];
            foreach (array_keys($delta) as $fieldName) {
                if (array_key_exists($fieldName, $metadata)) {
                    //-- blob
                    if ($metadata[$fieldName]['type'] == 'blob') {
                        continue;
                    }
                }
                if (is_string($model->{$fieldName})) {
                    if (trim($model->{$fieldName}) != $old[$fieldName]) {
                        $dirty[] = $fieldName;
                    }
                } elseif ($model->{$fieldName} != $old[$fieldName]) {
                    $dirty[] = $fieldName;
                }
            }
            if (!empty($dirty)) {
                $error[] = CrudJsonResponse::error(CrudHttpErrors::TARGET_DATA_MODIFIED_BY_OTHERS, null, [
                    'action' => __FUNCTION__,
                    'dirty' => $dirty,
                ])->getData();
                continue;
            }

            $data = array_merge($old, $delta);
            $r = $this->validateData($data, $old, __FUNCTION__);
            if ($r !== true) {
                $error[] = $r->getData();
                continue;
            }

            $data = [$data];
            $this->model->prepareDataToSave($data, $metadata);
            $data = $data[0];

            $model->fill($data);
            $model->{$keyName} = $data[$keyName];

            $afterOk = false;
            DB::beginTransaction();

            $dbOk = !$model->isDirty() ?: $model->save();

            if ($dbOk === true) {
                $afterOk = $this->afterUpdate($data, $model);
            }

            if ($dbOk == true and $afterOk === true) {
                DB::commit();
                $successful[] = $model;
            } else {
                DB::rollback();
                $error[] = ($afterOk instanceof \Illuminate\Http\JsonResponse)
                    ? $afterOk->getData()
                    : CrudJsonResponse::error(CrudHttpErrors::DATA_MODEL_UPDATE_FAIL, null, $model)->getData()
                ;
            }
        }

        $this->model->relationsToObjects($successful);
        $this->model->prepareDataToSend($successful, $metadata);

        $resultData = [
            'action' => __FUNCTION__,
            'successful' => $successful,
            'error' => $error,
        ];

        if (!empty($successful) and empty($error)) {
            return CrudJsonResponse::successful($resultData);
        }

        return CrudJsonResponse::error(empty($successful) ? CrudHttpErrors::ACTION_ERROR : CrudHttpErrors::PARTIALLY_SUCCESSFUL, null, $resultData);
    }

    /**
     * Action executed before each item destroy action
     *
     * @param  \Antares\Crud\CrudModel  $model
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function beforeDestroy(CrudModel $model)
    {
        return true;
    }

    /**
     * Action executed after each item destroy action
     *
     * @param  \Antares\Crud\CrudModel  $model
     * @return bool|\Illuminate\Http\JsonResponse
     */
    protected function afterDestroy(CrudModel $model)
    {
        return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $keyName = $this->model->getKeyName();

        $items = $this->request()->has('data.items') ? $this->request()->input('data.items') : [];
        if (empty($items)) {
            $items[$keyName] = $id;
        }

        if (Arr::isAssoc($items)) {
            $items = [$items];
        }

        $this->model->relationsFromObjects($items);

        $successful = [];
        $error = [];

        foreach ($items as $item) {
            if (!Arr::has($item, $keyName)) {
                $error[] = CrudJsonResponse::error(CrudHttpErrors::NO_PRIMARY_KEY_SUPPLIED, null, $item)->getData();
                continue;
            }

            $model = $this->getModelByPrimaryKey(__FUNCTION__, $item[$keyName]);
            if ($model instanceof \Illuminate\Http\JsonResponse) {
                $error[] = $model->getData();
                continue;
            }

            $r = $this->beforeDestroy($model);
            if ($r !== true) {
                $error[] = $r->getData();
                continue;
            }

            $afterOk = false;
            DB::beginTransaction();

            $dbOk = $model->delete();

            if ($dbOk === true) {
                $afterOk = $this->afterDestroy($model);
            }

            if ($dbOk === true and $afterOk === true) {
                DB::commit();
                $successful[] = $model;
            } else {
                DB::rollback();
                $error[] = ($afterOk instanceof \Illuminate\Http\JsonResponse)
                    ? $afterOk->getData()
                    : CrudJsonResponse::error(CrudHttpErrors::DATA_MODEL_DELETE_FAIL, null, $model)->getData()
                ;
            }
        }

        $this->model->relationsToObjects($successful);
        $this->model->prepareDataToSend($successful);

        $resultData = [
            'action' => __FUNCTION__,
            'successful' => $successful,
            'error' => $error,
        ];

        if (!empty($successful) and empty($error)) {
            return CrudJsonResponse::successful($resultData);
        }

        return CrudJsonResponse::error(empty($successful) ? CrudHttpErrors::ACTION_ERROR : CrudHttpErrors::PARTIALLY_SUCCESSFUL, null, $resultData);
    }
}
