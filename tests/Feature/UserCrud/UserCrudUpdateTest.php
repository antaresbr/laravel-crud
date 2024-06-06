<?php

namespace Antares\Tests\Feature\UserCrud;

use Antares\Crud\Http\CrudHttpErrors;
use Antares\Foundation\Arr;
use Antares\Foundation\Str;
use Antares\Tests\Package\AbstractTestCases\UserCrudAbstractTestCase;

class UserCrudUpdateTest extends UserCrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->crudAction = 'update';
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
    public function unauthenticated_update()
    {
        $this->localBootstrap();
        $this->metadataRequest_getUnauthenticated();
    }

    /** @test */
    public function seed_data()
    {
        $this->localBootstrap();
        $this->seedAndTestUsers(20);
    }

    /** @test */
    public function update_with_empty_data()
    {
        $this->bootstrapAndAuthUser();

        $data = [];
        $response = $this->modelUpdate($this->entrypoint, $data, 200);
        $json = $response->json();

        $this->assertIsArray($json);
        $this->assertEquals($this->make_noDataSuppliedError(), $json);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray($json['data']);

        $data['data'] = [];
        $response = $this->modelUpdate($this->entrypoint, $data, 200);
        $json = $response->json();
        $this->assertIsArray($json);
        $this->assertEquals($this->make_noDataSuppliedError(), $json);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray($json['data']);

        $data['data'] = [$this->makeModels(1)];
        $response = $this->modelUpdate($this->entrypoint, $data, 200);
        $json = $response->json();
        $this->assertIsArray($json);
        $this->assertEquals($this->make_noDataSuppliedError(), $json);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray($json['data']);
    }

    /** @test */
    public function update_with_with_different_array_lengths()
    {
        $this->bootstrapAndAuthUser();
        
        $data['data'] = ['delta' => $this->testModelClass::all()->random(1)->toArray()];

        $response = $this->modelUpdate($this->entrypoint, $data, 200);
        $json = $response->json();

        $this->assertIsArray($json);
        $this->assertArrayHasKey('status', $json);
        $this->assertEquals('error', $json['status']);
        $this->assertArrayHasKey('code', $json);
        $this->assertEquals(CrudHttpErrors::ARRAY_LENGTHS_DIFFER, $json['code']);
        $this->assertArrayHasKey('message', $json);
        $this->assertEquals(trans(CrudHttpErrors::MESSAGES[CrudHttpErrors::ARRAY_LENGTHS_DIFFER]), $json['message']);
        $this->assertArrayHasKey('data', $json);
        $this->assertIsArray(Arr::get($json, 'data'));
        $this->assertTrue(Arr::has($json, 'data.action'));
        $this->assertEquals($this->crudAction, Arr::get($json, 'data.action'));
        $this->assertTrue(Arr::has($json, 'data.delta'));
        $this->assertIsArray(Arr::get($json, 'data.delta'));
        $this->assertCount(1, Arr::get($json, 'data.delta'));
        $this->assertTrue(Arr::has($json, 'data.old'));
        $this->assertIsArray(Arr::get($json, 'data.old'));
        $this->assertCount(0, Arr::get($json, 'data.old'));
    }

    /** @test */
    public function update_one_with_error()
    {
        $this->bootstrapAndAuthUser();
        
        $old = $this->testModelClass::all()->random(1)->toArray();
        $data['data'] = [
            'delta' => [
                ['name' => null],
            ],
            'old' => $old,
        ];

        $response = $this->modelUpdate($this->entrypoint, $data, 200);
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
    public function update_one_with_dirty_data()
    {
        $this->bootstrapAndAuthUser();
        
        $old = $this->testModelClass::all()->random(1)->toArray();
        $old[0]['name'] = 'dirty data';
        $data['data'] = [
            'delta' => [
                ['name' => 'Changed User Name'],
            ],
            'old' => $old,
        ];

        $response = $this->modelUpdate($this->entrypoint, $data, 200);
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
        $this->assertResponseErrorItem_targetDataModifiedByOthers(0, $json, $data);
    }

    /** @test */
    public function update_one_successful()
    {
        $this->bootstrapAndAuthUser();
        
        $old = $this->testModelClass::all()->random(1)->toArray();
        $data['data'] = [
            'delta' => [
                [
                    'name' => 'Changed User Name',
                    'remember_token' => Str::random(16),
                ],
            ],
            'old' => $old,
        ];
        $expect = array_merge(Arr::get($data, 'data.old.0'), Arr::get($data, 'data.delta.0'));

        $response = $this->modelUpdate($this->entrypoint, $data);
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
        $this->assertModelData($expect, Arr::get($json, 'data.successful.0'));
        $this->assertTrue(Arr::has($json, 'data.error'));
        $this->assertIsArray(Arr::get($json, 'data.error'));
        $this->assertCount(0, Arr::get($json, 'data.error'));
    }

    /** @test */
    public function update_many_with_error()
    {
        $this->bootstrapAndAuthUser();
        
        $old = $this->testModelClass::all()->random(3)->toArray();
        $delta = [];
        foreach(array_keys($old) as $key) {
            $delta[] = [
                'name' => null,
            ];
        }
        $data['data'] = [
            'delta' => $delta,
            'old' => $old,
        ];

        $response = $this->modelUpdate($this->entrypoint, $data);
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
        foreach(array_keys(Arr::get($data, 'data.old')) as $key) {
            $this->assertResponseErrorItem_dataValidationError($key, $json, $data);
        }
    }

    /** @test */
    public function update_many_with_partial_success()
    {
        $this->bootstrapAndAuthUser();

        $old = $this->testModelClass::all()->random(5)->toArray();
        $delta = [];
        foreach(array_keys($old) as $key) {
            $delta[] = [
                'name' => "Changed User {$key} Name",
                'remember_token' => Str::random(16),
            ];
        }
        $delta[1]['name'] = null;
        $delta[3]['name'] = null;
        $data['data'] = [
            'delta' => $delta,
            'old' => $old,
        ];

        $response = $this->modelUpdate($this->entrypoint, $data, 200);
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
        $this->assertModelData(array_merge(Arr::get($data, 'data.old.0'), Arr::get($data, 'data.delta.0')), Arr::get($json, 'data.successful.0'));
        $this->assertModelData(array_merge(Arr::get($data, 'data.old.2'), Arr::get($data, 'data.delta.2')), Arr::get($json, 'data.successful.1'));
        $this->assertModelData(array_merge(Arr::get($data, 'data.old.4'), Arr::get($data, 'data.delta.4')), Arr::get($json, 'data.successful.2'));
        $this->assertTrue(Arr::has($json, 'data.error'));
        $this->assertIsArray(Arr::get($json, 'data.error'));
        $this->assertCount(2, Arr::get($json, 'data.error'));
        $this->assertResponseErrorItem_dataValidationError(0, $json, $data, 1);
        $this->assertResponseErrorItem_dataValidationError(1, $json, $data, 3);
    }

    /** @test */
    public function update_many_successful()
    {
        $this->bootstrapAndAuthUser();
        
        $old = $this->testModelClass::all()->random(5)->toArray();
        $delta = [];
        foreach(array_keys($old) as $key) {
            $delta[] = [
                'name' => "Changed User {$key} Name",
                'remember_token' => Str::random(16),
            ];
        }
        $data['data'] = [
            'delta' => $delta,
            'old' => $old,
        ];

        $response = $this->modelUpdate($this->entrypoint, $data);
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
        foreach(array_keys(Arr::get($data, 'data.old')) as $key) {
            $this->assertModelData(array_merge(Arr::get($data, "data.old.{$key}"), Arr::get($data, "data.delta.{$key}")), Arr::get($json, "data.successful.{$key}"));
        }
        $this->assertTrue(Arr::has($json, 'data.error'));
        $this->assertIsArray(Arr::get($json, 'data.error'));
        $this->assertCount(0, Arr::get($json, 'data.error'));
    }
}
