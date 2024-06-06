<?php

namespace Antares\Tests\Feature\GroupCrud;

use Antares\Crud\Http\CrudHttpErrors;
use Antares\Foundation\Arr;
use Antares\Tests\Package\AbstractTestCases\GroupCrudAbstractTestCase;

class GroupCrudStoreTest extends GroupCrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->crudAction = 'store';
    }

    /** @test */
    public function reset_database()
    {
        $this->resetDatabase();
    }

    /** @test */
    public function assert_refreshed_database()
    {
        $this->assertRefreshedDatabase();
    }

    /** @test */
    public function unauthenticated_store()
    {
        $this->localBootstrap();
        $this->metadataRequest_getUnauthenticated();
    }

    /** @test */
    public function seed_first_user()
    {
        $this->localBootstrap();
        $this->seedAndTestUsers(1);
    }

    /** @test */
    public function store_with_empty_data()
    {
        $this->bootstrapAndAuthUser();

        $data = [];
        $response = $this->modelStore($this->entrypoint, $data, 200);
        $json = $response->json();

        $this->assertIsArray($json);
        $this->assertEquals($this->make_noDataSuppliedError(), $json);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray($json['data']);

        $data['data'] = [];
        $response = $this->modelStore($this->entrypoint, $data, 200);
        $json = $response->json();
        $this->assertIsArray($json);
        $this->assertEquals($this->make_noDataSuppliedError(), $json);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray($json['data']);

        $data['data'] = [$this->makeModels(1)];
        $response = $this->modelStore($this->entrypoint, $data, 200);
        $json = $response->json();
        $this->assertIsArray($json);
        $this->assertEquals($this->make_noDataSuppliedError(), $json);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray($json['data']);
    }

    /** @test */
    public function store_one_with_error()
    {
        $this->bootstrapAndAuthUser();
        
        $data['data'] = ['items' => $this->makeModels(1)];
        Arr::forget($data, 'data.items.0.name');

        $response = $this->modelStore($this->entrypoint, $data, 200);
        $json = $response->json();

        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('error', $json['status']);
        $this->assertArrayHasKey('code', $json);
        $this->assertEquals(CrudHttpErrors::ACTION_ERROR, $json['code']);
        $this->assertArrayHasKey('message', $json);
        $this->assertEquals(trans(CrudHttpErrors::MESSAGES[CrudHttpErrors::ACTION_ERROR]), $json['message']);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray(Arr::get($json, 'data'));
        $this->assertTrue(Arr::has($json, 'data.action'));
        $this->assertEquals($this->crudAction, Arr::get($json, 'data.action'));
        $this->assertTrue(Arr::has($json, 'data.successful'));
        $this->assertIsArray(Arr::get($json, 'data.successful'));
        $this->assertCount(0, Arr::get($json, 'data.successful'));
        $this->assertTrue(Arr::has($json, 'data.error'));
        $this->assertIsArray(Arr::get($json, 'data.error'));
        $this->assertCount(1, Arr::get($json, 'data.error'));
        $this->assertResponseErrorItem_dataValidationError(0, $json, $data);
    }

    /** @test */
    public function store_one_successful()
    {
        $this->bootstrapAndAuthUser();
        
        $data['data'] = ['items' => $this->makeModels(1)];

        $response = $this->modelStore($this->entrypoint, $data);
        $json = $response->json();

        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('successful', $json['status']);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray(Arr::get($json, 'data'));
        $this->assertTrue(Arr::has($json, 'data.action'));
        $this->assertEquals($this->crudAction, Arr::get($json, 'data.action'));
        $this->assertTrue(Arr::has($json, 'data.successful'));
        $this->assertIsArray(Arr::get($json, 'data.successful'));
        $this->assertCount(1, Arr::get($json, 'data.successful'));
        $this->assertModelData(Arr::get($data, 'data.items.0'), Arr::get($json, 'data.successful.0'));
        $this->assertTrue(Arr::has($json, 'data.error'));
        $this->assertIsArray(Arr::get($json, 'data.error'));
        $this->assertCount(0, Arr::get($json, 'data.error'));

        $this->assertCount(1, $this->testModelClass::all());
    }

    /** @test */
    public function store_many_with_error()
    {
        $this->bootstrapAndAuthUser();
        
        $data['data'] = ['items' => $this->makeModels(3)];
        foreach(array_keys(Arr::get($data, 'data.items')) as $key) {
            Arr::forget($data, "data.items.{$key}.name");
        }

        $response = $this->modelStore($this->entrypoint, $data, 200);
        $json = $response->json();

        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('error', $json['status']);
        $this->assertArrayHasKey('code', $json);
        $this->assertEquals(CrudHttpErrors::ACTION_ERROR, $json['code']);
        $this->assertArrayHasKey('message', $json);
        $this->assertEquals(trans(CrudHttpErrors::MESSAGES[CrudHttpErrors::ACTION_ERROR]), $json['message']);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray(Arr::get($json, 'data'));
        $this->assertTrue(Arr::has($json, 'data.action'));
        $this->assertEquals($this->crudAction, Arr::get($json, 'data.action'));
        $this->assertTrue(Arr::has($json, 'data.successful'));
        $this->assertIsArray(Arr::get($json, 'data.successful'));
        $this->assertCount(0, Arr::get($json, 'data.successful'));
        $this->assertTrue(Arr::has($json, 'data.error'));
        $this->assertIsArray(Arr::get($json, 'data.error'));
        $this->assertCount(3, Arr::get($json, 'data.error'));
        foreach(array_keys(Arr::get($data, 'data.items')) as $key) {
            $this->assertResponseErrorItem_dataValidationError($key, $json, $data);
        }
    }

    /** @test */
    public function store_many_with_partial_success()
    {
        $this->bootstrapAndAuthUser();
        
        $data['data'] = ['items' => $this->makeModels(5)];
        Arr::forget($data, "data.items.1.name");
        Arr::forget($data, "data.items.3.name");

        $response = $this->modelStore($this->entrypoint, $data, 200);
        $json = $response->json();

        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('error', $json['status']);
        $this->assertArrayHasKey('code', $json);
        $this->assertEquals(CrudHttpErrors::PARTIALLY_SUCCESSFUL, $json['code']);
        $this->assertArrayHasKey('message', $json);
        $this->assertEquals(trans(CrudHttpErrors::MESSAGES[CrudHttpErrors::PARTIALLY_SUCCESSFUL]), $json['message']);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray(Arr::get($json, 'data'));
        $this->assertTrue(Arr::has($json, 'data.action'));
        $this->assertEquals($this->crudAction, Arr::get($json, 'data.action'));
        $this->assertTrue(Arr::has($json, 'data.successful'));
        $this->assertIsArray(Arr::get($json, 'data.successful'));
        $this->assertCount(3, Arr::get($json, 'data.successful'));
        $this->assertModelData(Arr::get($data, 'data.items.0'), Arr::get($json, 'data.successful.0'));
        $this->assertModelData(Arr::get($data, 'data.items.2'), Arr::get($json, 'data.successful.1'));
        $this->assertModelData(Arr::get($data, 'data.items.4'), Arr::get($json, 'data.successful.2'));
        $this->assertTrue(Arr::has($json, 'data.error'));
        $this->assertIsArray(Arr::get($json, 'data.error'));
        $this->assertCount(2, Arr::get($json, 'data.error'));
        $this->assertResponseErrorItem_dataValidationError(0, $json, $data, 1);
        $this->assertResponseErrorItem_dataValidationError(1, $json, $data, 3);

        $this->assertCount(4, $this->testModelClass::all());
    }

    /** @test */
    public function store_many_successful()
    {
        $this->bootstrapAndAuthUser();
        
        $data['data'] = ['items' => $this->makeModels(20)];

        $response = $this->modelStore($this->entrypoint, $data);
        $json = $response->json();

        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('successful', $json['status']);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray(Arr::get($json, 'data'));
        $this->assertTrue(Arr::has($json, 'data.action'));
        $this->assertEquals($this->crudAction, Arr::get($json, 'data.action'));
        $this->assertTrue(Arr::has($json, 'data.successful'));
        $this->assertIsArray(Arr::get($json, 'data.successful'));
        $this->assertCount(20, Arr::get($json, 'data.successful'));
        foreach(array_keys(Arr::get($data, 'data.items')) as $key) {
            $this->assertModelData(Arr::get($data, "data.items.{$key}"), Arr::get($json, "data.successful.{$key}"));
        }
        $this->assertTrue(Arr::has($json, 'data.error'));
        $this->assertIsArray(Arr::get($json, 'data.error'));
        $this->assertCount(0, Arr::get($json, 'data.error'));

        $this->assertCount(24, $this->testModelClass::all());
    }
}
