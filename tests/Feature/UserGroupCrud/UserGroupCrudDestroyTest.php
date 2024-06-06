<?php

namespace Antares\Tests\Feature\UserGroupCrud;

use Antares\Crud\Http\CrudHttpErrors;
use Antares\Foundation\Arr;
use Antares\Tests\Package\AbstractTestCases\UserGroupCrudAbstractTestCase;

class UserGroupCrudDestroyTest extends UserGroupCrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->crudAction = 'destroy';
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
    public function unauthenticated_destroy()
    {
        $this->localBootstrap();
        $this->metadataRequest_getUnauthenticated();
    }

    /** @test */
    public function seed_data()
    {
        $this->localBootstrap();
        $this->seedAndTestUsers(50);
        $this->seedAndTestGroups(20);
        $this->seedAndTestUserGroups(30);
    }

    /** @test */
    public function destroy_with_empty_data()
    {
        $this->bootstrapAndAuthUser();

        $data = [];
        $response = $this->modelDestroy($this->entrypoint, $data);
        $json = $response->json();
        $this->assertResponseError_destroyWithNoDataSupplied($json);

        $data['data'] = [];
        $response = $this->modelDestroy($this->entrypoint, $data);
        $json = $response->json();
        $this->assertResponseError_destroyWithNoDataSupplied($json);
        
        $data['data'] = [$this->makeModels(1)];
        $response = $this->modelDestroy($this->entrypoint, $data);
        $json = $response->json();
        $this->assertResponseError_destroyWithNoDataSupplied($json);
    }

    /** @test */
    public function destroy_one_with_error()
    {
        $this->bootstrapAndAuthUser();
        
        $item = $this->testModelClass::all()->random(1)->toArray()[0];
        $item['id'] = 999999;
        $data = [ 'data' => [
            'items' => [$item],
        ]];

        $response = $this->modelDestroy($this->entrypoint, $data, 200);
        $json = $response->json();

        $this->assertResponseErrorItem_targetDataModelNotFound(0, $json, $data);
    }

    /** @test */
    public function destroy_one_successful()
    {
        $this->bootstrapAndAuthUser();

        $data = [ 'data' => [
            'items' => $this->testModelClass::all()->random(1)->toArray(),
        ]];

        $response = $this->modelDestroy($this->entrypoint, $data);
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

        $this->assertCount(29, $this->testModelClass::all());
    }

    /** @test */
    public function destroy_one_successful_by_id()
    {
        $this->bootstrapAndAuthUser();

        $data = [ 'data' => [
            'items' => $this->testModelClass::all()->random(1)->toArray(),
        ]];

        $response = $this->modelDestroy($this->entrypoint, Arr::get($data, 'data.items.0.id'));
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

        $this->assertCount(28, $this->testModelClass::all());
    }

    /** @test */
    public function destroy_many_with_error()
    {
        $this->bootstrapAndAuthUser();
        
        $data = [ 'data' => [
            'items' => $this->testModelClass::all()->random(3)->toArray(),
        ]];
        foreach(array_keys(Arr::get($data, 'data.items')) as $key) {
            Arr::set($data, "data.items.{$key}.id", Arr::get($data, "data.items.{$key}.id") + 999000);
        }

        $response = $this->modelDestroy($this->entrypoint, $data);
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
            $this->assertResponseErrorItem_targetDataModelNotFound($key, $json, $data);
        }
    }

    /** @test */
    public function destroy_many_with_partial_success()
    {
        $this->bootstrapAndAuthUser();

        $data = [ 'data' => [
            'items' => $this->testModelClass::all()->random(5)->toArray(),
        ]];
        Arr::set($data, "data.items.1.id", Arr::get($data, "data.items.1.id") + 999000);
        Arr::set($data, "data.items.3.id", Arr::get($data, "data.items.3.id") + 999000);

        $response = $this->modelDestroy($this->entrypoint, $data);
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
        $this->assertModelData(array_merge(Arr::get($data, 'data.items.0')), Arr::get($json, 'data.successful.0'));
        $this->assertModelData(array_merge(Arr::get($data, 'data.items.2')), Arr::get($json, 'data.successful.1'));
        $this->assertModelData(array_merge(Arr::get($data, 'data.items.4')), Arr::get($json, 'data.successful.2'));
        $this->assertTrue(Arr::has($json, 'data.error'));
        $this->assertIsArray(Arr::get($json, 'data.error'));
        $this->assertCount(2, Arr::get($json, 'data.error'));
        $this->assertResponseErrorItem_targetDataModelNotFound(0, $json, $data, 1);
        $this->assertResponseErrorItem_targetDataModelNotFound(1, $json, $data, 3);

        $this->assertCount(25, $this->testModelClass::all());
    }

    /** @test */
    public function destroy_many_successful()
    {
        $this->bootstrapAndAuthUser();

        $data = [ 'data' => [
            'items' => $this->testModelClass::all()->random(5)->toArray(),
        ]];

        $response = $this->modelDestroy($this->entrypoint, $data);
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
        $this->assertCount(5, Arr::get($json, 'data.successful'));
        foreach(array_keys(Arr::get($data, 'data.items')) as $key) {
            $this->assertModelData(Arr::get($data, "data.items.{$key}"), Arr::get($json, "data.successful.{$key}"));
        }
        $this->assertTrue(Arr::has($json, 'data.error'));
        $this->assertIsArray(Arr::get($json, 'data.error'));
        $this->assertCount(0, Arr::get($json, 'data.error'));

        $this->assertCount(20, $this->testModelClass::all());
    }
}
