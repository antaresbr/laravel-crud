<?php

namespace Antares\Tests\Feature\UserGroupCrud;

use Antares\Crud\Http\CrudHttpErrors;
use Antares\Foundation\Arr;
use Antares\Tests\Package\AbstractTestCases\UserGroupCrudAbstractTestCase;
use Antares\Tests\Package\Models\AppGroup;
use Antares\Tests\Package\Models\AppUser;
use PHPUnit\Framework\Attributes\Test;

class UserGroupCrudUpdateTest extends UserGroupCrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->crudAction = 'update';
    }

    #[Test]
    public function reset_database()
    {
        $this->resetDatabase();
    }

    #[Test]
    public function assert_refreshed_database()
    {
        $this->assertRefreshedDatabase();
    }

    #[Test]
    public function unauthenticated_update()
    {
        $this->localBootstrap();
        $this->metadataRequest_getUnauthenticated();
    }

    #[Test]
    public function seed_data()
    {
        $this->localBootstrap();
        $this->seedAndTestUsers(30);
        $this->seedAndTestGroups(10);
        $this->seedAndTestUserGroups(30);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function update_one_successful()
    {
        $this->bootstrapAndAuthUser();

        $userCount = AppUser::all()->count();
        $old = $this->testModelClass::all()->random(1)->toArray();
        $data['data'] = [
            'delta' => [
                [
                    'user_id' => rand(1, $userCount),
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

    #[Test]
    public function update_many_successful()
    {
        $this->bootstrapAndAuthUser();
        
        $userCount = AppUser::all()->count();
        $groupCount = AppGroup::all()->count();
        
        $old = $this->testModelClass::all()->random(5)->toArray();
        $delta = [];
        foreach(array_keys($old) as $key) {
            $delta[] = [
                'user_id' => rand(1, $userCount),
                'group_id' => rand(1, $groupCount),
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
