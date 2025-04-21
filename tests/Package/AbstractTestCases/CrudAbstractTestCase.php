<?php

namespace Antares\Tests\Package\AbstractTestCases;

use Antares\Crud\Http\CrudHttpErrors;
use Antares\Crud\Metadata\Filter\Filter;
use Antares\Tests\Package\TestCase;
use Antares\Foundation\Arr;
use Antares\Foundation\Obj;
use Antares\Tests\Package\Models\AppUser;
use Antares\Tests\Package\Traits\AuthUserTrait;
use Antares\Tests\TestCase\Traits\ResetDatabaseTrait;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

abstract class CrudAbstractTestCase extends TestCase
{
    use AuthUserTrait;
    use ResetDatabaseTrait;

    protected $entrypoint;
    protected $crudAction;
    protected $testModelClass;
    protected $testErrorFieldName;

    protected function localBootstrap()
    {
        $this->testErrorFieldName = 'name';

        $this->userModelClass = AppUser::class;
        $this->assertEquals($this->getUserModelClass(), AppUser::class);
    }

    protected function bootstrapAndAuthUser($id = 1)
    {
        $this->localBootstrap();

        $user = $this->userModelClass::find($id);
        if (!$user) {
            $user = $this->userModelClass::all()->first();
        }
        if (!$user) {
            $this->seedAndTestUsers(1);
        }
        
        return $this->authUser($id);
    }

    protected function makeModels($amount)
    {
        $data = [];
        for($i = 1; $i <= $amount; $i++) {
            $data[] = $this->testModelClass::factory()->make()->toArray();
        }
        return $data;
    }

    protected function modelDestroy($entrypoint, $idOrData, $httpStatus = 200)
    {
        if (!is_array($idOrData)) {
            $id = $idOrData;
            $idOrData = [];
        } else {
            $id = '_';
        }
        $url = config('package.route.prefix.api') . "/{$entrypoint}/{$id}";
        $response = $this->delete($url, $idOrData);
        $response->assertStatus($httpStatus);
        return $response;
    }

    protected function modelIndex($entrypoint, $data, $httpStatus = 200)
    {
        $data['_method'] = 'GET';
        $response = $this->post(config('package.route.prefix.api') . "/{$entrypoint}", $data);
        $response->assertStatus($httpStatus);
        return $response;
    }

    protected function modelMetadata($entrypoint, $httpStatus = 200)
    {
        $response = $this->get(config('package.route.prefix.api') . "/{$entrypoint}/_get-metadata_");
        $response->assertStatus($httpStatus);
        return $response;
    }

    protected function modelSearch($entrypoint, $data, $httpStatus = 200)
    {
        $data['_method'] = 'GET';
        $response = $this->post(config('package.route.prefix.api') . "/{$entrypoint}/_search_", $data);
        $response->assertStatus($httpStatus);
        return $response;
    }

    protected function modelShow($entrypoint, $idOrData, $httpStatus = 200)
    {
        if (!is_array($idOrData)) {
            $id = $idOrData;
            $idOrData = [];
        } else {
            $id = '_';
        }
        if (!array_key_exists('_method', $idOrData)) {
            $idOrData['_method'] = 'GET';
        }
        $url = config('package.route.prefix.api') . "/{$entrypoint}/{$id}";
        $response = $this->post($url, $idOrData);
        $response->assertStatus($httpStatus);
        return $response;
    }

    protected function modelStore($entrypoint, $data, $httpStatus = 201)
    {
        $response = $this->post(config('package.route.prefix.api') . "/{$entrypoint}", $data);
        $response->assertStatus($httpStatus);
        return $response;
    }

    protected function modelUpdate($entrypoint, $data, $httpStatus = 200)
    {
        $response = $this->put(config('package.route.prefix.api') . "/{$entrypoint}/_", $data);
        $response->assertStatus($httpStatus);
        return $response;
    }

    protected function assertModelData($expect, $actual)
    {
        //-- to be overridden in descendent classes
    }

    protected function make_noDataSuppliedError()
    {
        $data = [
            'status' => 'error',
            'code' => CrudHttpErrors::NO_DATA_SUPPLIED,
            'message' => trans(CrudHttpErrors::MESSAGES[CrudHttpErrors::NO_DATA_SUPPLIED]),
            'data' => [
                'action' => $this->crudAction,
            ],
        ];
        if ($this->crudAction == 'update') {
            $data['data']['delta'] = [];
        } else {
            $data['data']['items'] = [];
        }

        return $data;
    }

    protected function assertResponseErrorItem($errorCode, $json)
    {
        $this->assertTrue(Arr::has($json, "status"));
        $this->assertEquals('error', Arr::get($json, "status"));
        $this->assertTrue(Arr::has($json, "code"));
        $this->assertEquals($errorCode, Arr::get($json, "code"));
        $this->assertTrue(Arr::has($json, "message"));
        $this->assertEquals(trans(CrudHttpErrors::MESSAGES[$errorCode]), Arr::get($json, "message"));
        $this->assertTrue(Arr::has($json, "data"));
        $this->assertIsArray(Arr::get($json, "data"));
        $this->assertTrue(Arr::has($json, "data.action"));
        $parent_Action = Arr::has($json, "data.parent_action");
        if (!empty($parent_Action)) {
            $this->assertEquals($this->crudAction, $parent_Action);
        } else{
            $this->assertEquals($this->crudAction, Arr::get($json, "data.action"));
        }
    }

    protected function assertResponseErrorItem_getErrorItem($jsonKey, $json)
    {
        if ($this->crudAction == 'show') {
            $errorItem = $json;
        } else {
            $this->assertTrue(Arr::has($json, "data.error.{$jsonKey}"));
            $this->assertIsArray(Arr::get($json, "data.error.{$jsonKey}"));
            $errorItem = Arr::get($json, "data.error.{$jsonKey}");
        }
        return $errorItem;
    }

    protected function assertResponseErrorItem_dataValidationError($jsonKey, $json, $data, $dataKey = null)
    {
        !is_null($dataKey) or $dataKey = $jsonKey;

        $errorItem = $this->assertResponseErrorItem_getErrorItem($jsonKey, $json);
        $this->assertResponseErrorItem(CrudHttpErrors::DATA_VALIDATION_ERROR, $errorItem);

        $this->assertTrue(Arr::has($json, "data.error.{$jsonKey}.data.errors"));
        $this->assertIsArray(Arr::get($json, "data.error.{$jsonKey}.data.errors"));
        $this->assertArrayHasKey($this->testErrorFieldName, Arr::get($json, "data.error.{$jsonKey}.data.errors"));
        
        if ($this->crudAction == 'update') {
            $expect = array_merge(Arr::get($data, "data.old.{$dataKey}"), Arr::get($data, "data.delta.{$dataKey}"));
        } else {
            $expect = Arr::get($data, "data.items.{$dataKey}");
        }
        $this->assertModelData($expect, Arr::get($json, "data.error.{$jsonKey}.data.source"));
    }

    protected function assertResponseErrorItem_targetDataModifiedByOthers($jsonKey, $json, $data, $dataKey = null)
    {
        $errorItem = $this->assertResponseErrorItem_getErrorItem($jsonKey, $json);
        $this->assertResponseErrorItem(CrudHttpErrors::TARGET_DATA_MODIFIED_BY_OTHERS, $errorItem);

        $dirty = [$this->testErrorFieldName];
        $this->assertTrue(Arr::has($json, "data.error.{$jsonKey}.data.dirty"));
        $this->assertEquals($dirty, Arr::get($json, "data.error.{$jsonKey}.data.dirty"));
    }

    protected function assertResponseErrorItem_targetDataModelNotFound($jsonKey, $json, $data, $dataKey = null)
    {
        !is_null($dataKey) or $dataKey = $jsonKey;

        $errorItem = $this->assertResponseErrorItem_getErrorItem($jsonKey, $json);
        $this->assertResponseErrorItem(CrudHttpErrors::TARGET_DATA_MODEL_NOT_FOUND, $errorItem);

        $this->assertTrue(Arr::has($errorItem, "data.action"));
        $this->assertEquals('primaryKey', Arr::get($errorItem, "data.action"));

        $modelKeyName = (new $this->testModelClass())->getKeyName();
        $target = [$modelKeyName => Arr::get($data, "data.items.{$dataKey}.{$modelKeyName}")];
        $this->assertTrue(Arr::has($errorItem, "data.target"));
        $this->assertEquals($target, Arr::get($errorItem, "data.target"));
    }

    protected function assertResponseError_destroyWithNoDataSupplied($json)
    {
        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('error', $json['status']);
        $this->assertArrayHasKey('code', $json);
        $this->assertEquals(CrudHttpErrors::ACTION_ERROR, $json['code']);
        $this->assertArrayHasKey('message', $json);
        $this->assertEquals(trans(CrudHttpErrors::MESSAGES[CrudHttpErrors::ACTION_ERROR]), $json['message']);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray($json['data']);
        $this->assertTrue(Arr::has($json, 'data.action'));
        $this->assertEquals($this->crudAction, Arr::get($json, 'data.action'));
        $this->assertTrue(Arr::has($json, 'data.successful'));
        $this->assertEquals([], Arr::get($json, 'data.successful'));
        $this->assertTrue(Arr::has($json, 'data.error'));
        $this->assertIsArray(Arr::get($json, 'data.error'));
        $this->assertCount(1, Arr::get($json, 'data.error'));
        $this->assertIsArray(Arr::get($json, 'data.error.0'));
        $this->assertTrue(Arr::has($json, 'data.error.0.status'));
        $this->assertEquals('error', Arr::get($json, 'data.error.0.status'));
        $this->assertTrue(Arr::has($json, 'data.error.0.code'));
        $this->assertEquals(CrudHttpErrors::DATA_VALIDATION_ERROR, Arr::get($json, 'data.error.0.code'));
        $this->assertTrue(Arr::has($json, 'data.error.0.message'));
        $this->assertEquals(trans(CrudHttpErrors::MESSAGES[CrudHttpErrors::DATA_VALIDATION_ERROR]), Arr::get($json, 'data.error.0.message'));
        $this->assertTrue(Arr::has($json, 'data.error.0.data'));
        $this->assertIsArray(Arr::get($json, 'data.error.0.data'));
        $this->assertTrue(Arr::has($json, 'data.error.0.data.action'));
        $this->assertEquals('primaryKey', Arr::get($json, 'data.error.0.data.action'));
        $this->assertTrue(Arr::has($json, 'data.error.0.data.parent_action'));
        $this->assertEquals($this->crudAction, Arr::get($json, 'data.error.0.data.parent_action'));
        $this->assertTrue(Arr::has($json, 'data.error.0.data.errors'));
        $this->assertIsArray(Arr::get($json, 'data.error.0.data.errors'));
        $this->assertTrue(Arr::has($json, 'data.error.0.data.errors.id'));
        $this->assertIsArray(Arr::get($json, 'data.error.0.data.errors.id'));
        $this->assertTrue(Arr::has($json, 'data.error.0.data.errors.id.0'));
        $this->assertEquals('The ID [id] field must be an integer.', Arr::get($json, 'data.error.0.data.errors.id.0'));
        $this->assertTrue(Arr::has($json, 'data.error.0.data.source'));
        $this->assertIsArray(Arr::get($json, 'data.error.0.data.source'));
        $this->assertTrue(Arr::has($json, 'data.error.0.data.source.id'));
        $this->assertEquals('_', Arr::get($json, 'data.error.0.data.source.id'));
    }

    protected function assertResponseError_showWithNoDataSupplied($json)
    {
        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('error', $json['status']);
        $this->assertArrayHasKey('code', $json);
        $this->assertEquals(CrudHttpErrors::DATA_VALIDATION_ERROR, $json['code']);
        $this->assertArrayHasKey('message', $json);
        $this->assertEquals(trans(CrudHttpErrors::MESSAGES[CrudHttpErrors::DATA_VALIDATION_ERROR]), $json['message']);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray(Arr::get($json, 'data'));
        $this->assertTrue(Arr::has($json, 'data.action'));
        $this->assertEquals('primaryKey', Arr::get($json, 'data.action'));
        $this->assertTrue(Arr::has($json, 'data.parent_action'));
        $this->assertEquals($this->crudAction, Arr::get($json, 'data.parent_action'));
        $this->assertTrue(Arr::has($json, 'data.errors'));
        $this->assertIsArray(Arr::get($json, 'data.errors'));
        $this->assertCount(1, Arr::get($json, 'data.errors'));
        $this->assertTrue(Arr::has($json, 'data.errors.id'));
        $this->assertIsArray(Arr::get($json, 'data.errors.id'));
        $modelKeyName = (new $this->testModelClass())->getkeyName();
        $this->assertEquals(["The ID [{$modelKeyName}] field must be an integer."],Arr::get($json, "data.errors.{$modelKeyName}"));
        $this->assertTrue(Arr::has($json, 'data.source'));
        $this->assertIsArray(Arr::get($json, 'data.source'));
        $this->assertCount(1, Arr::get($json, 'data.source'));
        $this->assertTrue(Arr::has($json, "data.source.{$modelKeyName}"));
        $this->assertEquals('_', Arr::has($json, "data.source.{$modelKeyName}"));
    }

    protected function assertResponseSuccessful_indexAndSearch($json, $expect)
    {
        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('successful', $json['status']);
        $this->assertArrayHasKey('message', $json);
        $this->assertNull($json['message']);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray(Arr::get($json, 'data'));
        $this->assertTrue(Arr::has($json, 'data.action'));
        $this->assertEquals($this->crudAction, Arr::get($json, 'data.action'));
        $this->assertTrue(Arr::has($json, 'data.metadata'));
        $this->assertIsArray(Arr::get($json, 'data.metadata'));
        $this->assertTrue(Arr::has($json, 'data.metadata.pagination'));
        $this->assertIsArray(Arr::get($json, 'data.metadata.pagination'));
        $this->asserttrue(Arr::has($json, 'data.metadata.pagination.currentPage'));
        $this->asserttrue(Arr::has($json, 'data.metadata.pagination.lastPage'));
        $this->asserttrue(Arr::has($json, 'data.metadata.pagination.perPage'));
        $this->asserttrue(Arr::has($json, 'data.metadata.pagination.from'));
        $this->asserttrue(Arr::has($json, 'data.metadata.pagination.to'));
        $this->asserttrue(Arr::has($json, 'data.metadata.pagination.total'));
        $this->asserttrue(Arr::has($json, 'data.metadata.pagination.targetPage'));
        $this->assertTrue(Arr::has($json, 'data.items'));
        $this->assertIsArray(Arr::get($json, 'data.items'));
        $pagination = Arr::get($json, 'data.metadata.pagination');
        $this->assertEquals(Arr::get($expect, 'pagination.currentPage'), $pagination['currentPage']);
        $this->assertEquals(Arr::get($expect, 'pagination.lastPage'), $pagination['lastPage']);
        $this->assertEquals(Arr::get($expect, 'pagination.perPage'), $pagination['perPage']);
        $this->assertEquals(Arr::get($expect, 'pagination.from'), $pagination['from']);
        $this->assertEquals(Arr::get($expect, 'pagination.to'), $pagination['to']);
        $this->assertEquals(Arr::get($expect, 'pagination.total'), $pagination['total']);
        $this->assertNull($pagination['targetPage']);
        $this->assertTrue(Arr::has($json, 'data.items'));
        $items = Arr::get($json, 'data.items');
        $this->assertIsArray($items);
        $this->assertCount($pagination['to'] - $pagination['from'] + 1, $items);
        $firstId = (($pagination['currentPage'] - 1) * $pagination['perPage']) + Arr::get($expect, 'idStart');
        $this->assertEquals($firstId, Arr::get($items, "0.id"));
        $lastKey = count($items) - 1;
        $lastId = (($pagination['currentPage'] - 1) * $pagination['perPage']) + Arr::get($expect, 'idStart') + count($items) - 1;
        $this->assertEquals($lastId, Arr::get($items, "{$lastKey}.id"));
    }

    protected function indexAndSearchRequest_makeExpect($targetPage, $perPage, $idStart, $idEnd, $total)
    {
        !empty($targetPage) or $targetPage = 1;
        !empty($perPage) or $perPage = (new $this->testModelClass())->defaultPerPage();

        $lastPage = intdiv($total, $perPage);
        ($total % $perPage == 0) or $lastPage++;
        $itemsFrom = (($targetPage - 1) * $perPage) + 1;
        $itemsTo = ($targetPage * $perPage);
        ($itemsTo <= $total) or $itemsTo = $total;
        $expect = [
            'idStart' => $idStart,
            'idEnd' => $idEnd,
            'targetPage' => $targetPage,
            'pagination' => [
                'currentPage' => $targetPage,
                'lastPage' => $lastPage,
                'perPage' => $perPage,
                'from' => $itemsFrom,
                'to' => $itemsTo,
                'total' => $total,
            ]
        ];

        return $expect;
    }

    protected function indexAndSearchRequest_withDefaultData($targetPage, $idStart, $idEnd, $total, $ignoreStatic = false)
    {
        $expect = $this->indexAndSearchRequest_makeExpect($targetPage, null, $idStart, $idEnd, $total);

        $data = [];
        !$ignoreStatic or Arr::set($data, 'data.metadata.filters.ignoreStatic', true);
        (Arr::get($expect, 'targetPage') == 1) or Arr::set($data, 'data.metadata.pagination.targetPage', Arr::get($expect, 'targetPage'));
        if ($this->crudAction == 'search') {
            $response = $this->modelSearch($this->entrypoint, $data);
        } else {
            $response = $this->modelIndex($this->entrypoint, $data);
        }
        $json = $response->json();

        $this->assertResponseSuccessful_indexAndSearch($json, $expect);
    }

    protected function indexAndSearchRequest_withFiltersAndPagination($targetPage, $perpage, $idStart, $idEnd, $total, $ignoreStatic = false)
    {
        $expect = $this->indexAndSearchRequest_makeExpect($targetPage, $perpage, $idStart, $idEnd, $total);

        $data = [];
        !$ignoreStatic or Arr::set($data, 'data.metadata.filters.ignoreStatic', true);
        Arr::set($data, 'data.metadata.filters.custom', [
            Filter::make(['column' => 'id', 'operator' => '>=', 'value' => Arr::get($expect, 'idStart')]),
            Filter::make(['column' => 'id', 'operator' => '<=', 'value' => Arr::get($expect, 'idEnd')]),
        ]);
        Arr::set($data, 'data.metadata.pagination.perPage', Arr::get($expect, 'pagination.perPage'));
        Arr::set($data, 'data.metadata.pagination.targetPage', Arr::get($expect, 'targetPage'));
        if ($this->crudAction == 'search') {
            $response = $this->modelSearch($this->entrypoint, $data);
        } else {
            $response = $this->modelIndex($this->entrypoint, $data);
        }
        $json = $response->json();

        $this->assertResponseSuccessful_indexAndSearch($json, $expect);
    }

    protected function metadataRequest_getUnauthenticated()
    {
        $this->withoutExceptionHandling();
        try {
            $this->modelMetadata($this->entrypoint, 500);
        } catch (RouteNotFoundException $e) {
            $this->assertEquals('Route [login] not defined.', $e->getMessage());
            return;
        }
        $this->fail(sprintf('The expected "%s" exception was not thrown.', RouteNotFoundException::class));
    }

    protected function metadataRequest_get()
    {
        $response = $this->modelMetadata($this->entrypoint);
        $json = $response->json();

        $this->assertEquals('successful', Arr::get($json, 'status'));
        $this->assertIsArray($json);
        $this->assertIsArray(Arr::get($json, 'data'));
        $this->assertEquals('metadata', Arr::get($json, 'data.action'));
        $this->assertIsArray(Arr::get($json, 'data.metadata'));
        
        $model = new $this->testModelClass;
        $this->assertEquals($model->getApi(), Arr::get($json, 'data.metadata.api'));
        $this->assertEquals($model->getTable(), Arr::get($json, 'data.metadata.table'));
        $this->assertEquals(Obj::get($model, 'primaryKey'), Arr::get($json, 'data.metadata.primaryKey'));
        $this->assertFalse(Arr::get($json, 'data.metadata.menu'));
    }
}
