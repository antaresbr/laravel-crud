<?php

namespace Antares\Tests\Feature\UserGroupCrud;

use Antares\Foundation\Arr;
use Antares\Tests\Package\AbstractTestCases\UserGroupCrudAbstractTestCase;

class UserGroupCrudShowTest extends UserGroupCrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->crudAction = 'show';
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
    public function unauthenticated_show()
    {
        $this->localBootstrap();
        $this->metadataRequest_getUnauthenticated();
    }

    /** @test */
    public function seed_data()
    {
        $this->localBootstrap();
        $this->seedAndTestUsers(30);
        $this->seedAndTestGroups(10);
        $this->seedAndTestUserGroups(30);
    }

    /** @test */
    public function show_with_empty_data()
    {
        $this->bootstrapAndAuthUser();

        $data = [];
        $response = $this->modelShow($this->entrypoint, $data);
        $json = $response->json();
        $this->assertResponseError_showWithNoDataSupplied($json);

        $data['data'] = [];
        $response = $this->modelShow($this->entrypoint, $data);
        $json = $response->json();
        $this->assertResponseError_showWithNoDataSupplied($json);

        $data['data'] = [$this->makeModels(1)];
        $response = $this->modelShow($this->entrypoint, $data);
        $json = $response->json();
        $this->assertResponseError_showWithNoDataSupplied($json);
    }

    /** @test */
    public function show_one_with_error()
    {
        $this->bootstrapAndAuthUser();
        
        $items = $this->testModelClass::all()->random(1)->toArray();
        foreach(array_keys($items) as $key) {
            Arr::set($items, "{$key}.id", Arr::get($items, "{$key}.id") + 999000);
        }
        $data = [ 'data' => [
            'items' => $items,
        ]];

        $response = $this->modelShow($this->entrypoint, $data);
        $json = $response->json();
        $this->assertResponseErrorItem_targetDataModelNotFound(0, $json, $data);

        $response = $this->modelShow($this->entrypoint, Arr::get($data, 'data.items.0.id'));
        $json = $response->json();
        $this->assertResponseErrorItem_targetDataModelNotFound(0, $json, $data);
    }

    /** @test */
    public function show_one_with_error_by_id()
    {
        $this->bootstrapAndAuthUser();
        
        $items = $this->testModelClass::all()->random(1)->toArray();
        foreach(array_keys($items) as $key) {
            Arr::set($items, "{$key}.id", Arr::get($items, "{$key}.id") + 999000);
        }
        $data = [ 'data' => [
            'items' => $items,
        ]];

        $response = $this->modelShow($this->entrypoint, Arr::get($data, 'data.items.0.id'));
        $json = $response->json();
        $this->assertResponseErrorItem_targetDataModelNotFound(0, $json, $data);
    }

    /** @test */
    public function show_one_successful()
    {
        $this->bootstrapAndAuthUser();

        $data = [ 'data' => [
            'items' => $this->testModelClass::all()->random(1)->toArray(),
        ]];

        $response = $this->modelShow($this->entrypoint, $data);
        $json = $response->json();
        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('successful', $json['status']);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray(Arr::get($json, 'data'));
        $this->assertTrue(Arr::has($json, 'data.action'));
        $this->assertEquals($this->crudAction, Arr::get($json, 'data.action'));
        $this->assertTrue(Arr::has($json, 'data.items'));
        $this->assertIsArray(Arr::get($json, 'data.items'));
        $this->assertCount(1, Arr::get($json, 'data.items'));
        $this->assertModelData(Arr::get($data, 'data.items.0'), Arr::get($json, 'data.items.0'));
    }

    /** @test */
    public function show_one_successful_by_id()
    {
        $this->bootstrapAndAuthUser();

        $data = [ 'data' => [
            'items' => $this->testModelClass::all()->random(1)->toArray(),
        ]];

        $response = $this->modelShow($this->entrypoint, Arr::get($data, 'data.items.0.id'));
        $json = $response->json();
        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('successful', $json['status']);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray(Arr::get($json, 'data'));
        $this->assertTrue(Arr::has($json, 'data.action'));
        $this->assertEquals($this->crudAction, Arr::get($json, 'data.action'));
        $this->assertTrue(Arr::has($json, 'data.items'));
        $this->assertIsArray(Arr::get($json, 'data.items'));
        $this->assertCount(1, Arr::get($json, 'data.items'));
        $this->assertModelData(Arr::get($data, 'data.items.0'), Arr::get($json, 'data.items.0'));
    }
}
