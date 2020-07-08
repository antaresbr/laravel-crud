<?php

namespace Antares\Crud;

use Antares\Crud\Http\CrudHttpErrors;
use Antares\Crud\Http\CrudJsonResponse;
use Antares\Support\Arr;
use Antares\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\AbstractPaginator;

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
     * Acessor for menuId property
     *
     * @return string
     */
    public function menuId()
    {
        return property_exists($this, 'mneuId') ? $this->menuId : '';
    }

    /**
     * Crud validador for this handler
     *
     * @var CrudValidator
     */
    protected $validator;

    /**
     * Authorize action
     *
     * @param string $action
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function authorize($action)
    {
        if (empty($this->menuId())) {
            return CrudJsonResponse::error(CrudHttpErrors::MENUID_NOT_DEFINED, null, [
                'action' => $action,
            ]);
        }

        $action = Str::join('/', $this->menuId(), $action);

        //-- TODO : implement authorize function in laravel-acl
        // return Acl::authorize($action);

        return true;
    }

    /**
     * Validate some data for the supplied action
     *
     * @param array $data
     * @param string $action
     * @param string $parentAction
     * @param array $pkOptions
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function validateData($data, $action, $parentAction = null, $pkOptions = [])
    {
        if ($this->validator) {
            if (!$this->validator->validate($action, $data, $pkOptions)) {
                return CrudJsonResponse::error(CrudHttpErrors::DATA_VALIDATION_ERROR, null, [
                    'action' => $action,
                    'parent_action' => $parentAction,
                    'errors' => $this->validator->errors(),
                    'data' => $data,
                ]);
            }
        }

        return true;
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

        if (empty($attributes)) {
            $attributes = $this->model->getAttributes();
        }

        $source = $this->request()->has($dataPrefix) ? $this->request()->input($dataPrefix) : [];

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
            if ($this->request()->has("data.primarykey.{$keyName}")) {
                $keyValue = $this->request()->input("data.primarykey.{$keyName}");
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

        $r = $this->validateData($data, 'primarykey', $action, ['includeUniqueRule' => false]);
        if ($r !== true) {
            return $r;
        }

        if ($model = $this->model->query()->find($data[$keyName])) {
            return $model;
        }

        return CrudJsonResponse::error(CrudHttpErrors::TARGET_DATA_MODEL_NOT_FOUND, null, [
            'action' => 'primarykey',
            'parent_action' => $action,
            'target' => $data,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $r = $this->authorize(__FUNCTION__);
        if ($r !== true) {
            return $r;
        }

        if ($request->has('data.meta.pagination.target_page')) {
            $request->merge(['page' => $request->input('data.meta.pagination.target_page')]);
        }

        $per_page = Arr::get($this->model->metadata(), 'pagination.per_page');

        if ($request->has('data.meta.pagination.per_page')) {
            $per_page = $request->input('data.meta.pagination.per_page');
        }

        $resource = ($per_page >= 0) ? $this->model->paginate($per_page) : $this->model->all();

        $meta = $this->model->metadata();
        $meta['pagination'] = CrudPagination::make($resource)->toArray();

        return CrudJsonResponse::successful([
            'action' => __FUNCTION__,
            'meta' => $meta,
            'items' => ($resource instanceof AbstractPaginator) ? $resource->items() : $resource,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $r = $this->authorize(__FUNCTION__);
        if ($r !== true) {
            return $r;
        }

        $data = $this->attributesFromRequest('new');

        $r = $this->validateData($data, __FUNCTION__);
        if ($r !== true) {
            return $r;
        }

        $keyName = $this->model->getKeyName();

        $this->model->fill($data);
        if (Arr::has($data, $keyName)) {
            $this->model->{$keyName} = Arr::get($data, $keyName);
        }

        if ($this->model->save()) {
            return CrudJsonResponse::successful([
                'action' => __FUNCTION__,
                'items' => [$this->model],
            ], Response::HTTP_CREATED);
        }

        return CrudJsonResponse::error(CrudHttpErrors::DATA_MODEL_CREATION_ERROR, null, [
            'action' => __FUNCTION__,
            'items' => [$this->model],
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $r = $this->authorize(__FUNCTION__);
        if ($r !== true) {
            return $r;
        }

        $model = $this->getModelByPrimaryKey(__FUNCTION__, $id);

        if ($model instanceof \Illuminate\Http\JsonResponse) {
            return $model;
        }

        return CrudJsonResponse::successful([
            'action' => __FUNCTION__,
            'items' => [$model],
        ]);
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
        $r = $this->authorize(__FUNCTION__);
        if ($r !== true) {
            return $r;
        }

        $model = $this->getModelByPrimaryKey(__FUNCTION__, $id);

        if ($model instanceof \Illuminate\Http\JsonResponse) {
            return $model;
        }

        $old = $this->attributesFromRequest('old');
        $delta = $this->attributesFromRequest('delta');
        $data = array_merge($old, $delta);

        $keyName = $this->model->getKeyName();
        $r = $this->validateData($data, __FUNCTION__, null, Arr::has($old, $keyName) ? ['uniqueExceptId' => $old[$keyName]] : []);
        if ($r !== true) {
            return $r;
        }

        $dirty = [];
        foreach ($old as $fieldName => $fieldValue) {
            if ($model->{$fieldName} != $fieldValue) {
                $dirty[] = $fieldName;
            }
        }
        if (!empty($dirty)) {
            return CrudJsonResponse::error(CrudHttpErrors::TARGET_DATA_MODIFIED_BY_OTHERS, null, [
                'action' => __FUNCTION__,
                'dirty' => $dirty,
            ]);
        }

        if (Arr::has($delta, $keyName)) {
            $model->{$keyName} = $data[$keyName];
        }

        if ($model->isDirty()) {
            if (!$model->update($data)) {
                return CrudJsonResponse::error(CrudHttpErrors::DATA_MODEL_UPDATE_FAIL, null, [
                    'action' => __FUNCTION__,
                    'items' => [$model],
                ]);
            }
        }

        return CrudJsonResponse::successful([
            'action' => __FUNCTION__,
            'items' => [$model],
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $r = $this->authorize(__FUNCTION__);
        if ($r !== true) {
            return $r;
        }

        $model = $this->getModelByPrimaryKey(__FUNCTION__, $id);

        if ($model instanceof \Illuminate\Http\JsonResponse) {
            return $model;
        }

        if (!$model->delete()) {
            return CrudJsonResponse::error(CrudHttpErrors::DATA_MODEL_DELETE_FAIL, null, [
                'action' => __FUNCTION__,
                'items' => [$model],
            ]);
        }

        return CrudJsonResponse::successful([
            'action' => __FUNCTION__,
            'items' => [$model],
        ]);
    }
}
