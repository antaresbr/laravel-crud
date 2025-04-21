<?php

namespace Antares\Tests\Feature\UserGroupCrud;

use Antares\Crud\Http\CrudHttpErrors;
use Antares\Foundation\Arr;
use Antares\Tests\Package\AbstractTestCases\UserGroupCrudAbstractTestCase;
use PHPUnit\Framework\Attributes\Test;

class UserGroupCrudStoreTest extends UserGroupCrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->crudAction = 'store';
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
    public function unauthenticated_store()
    {
        $this->localBootstrap();
        $this->metadataRequest_getUnauthenticated();
    }

    #[Test]
    public function seed_first_user()
    {
        $this->localBootstrap();
        $this->seedAndTestUsers(30);
        $this->seedAndTestGroups(10);
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

        $this->assertCount(21, $this->testModelClass::all());
    }
}
