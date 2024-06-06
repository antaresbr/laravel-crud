<?php

namespace Antares\Tests\Package\AbstractTestCases;

use Antares\Foundation\Arr;
use Antares\Tests\Package\Models\AppUserGroup;

abstract class UserGroupCrudAbstractTestCase extends CrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->testModelClass = AppUserGroup::class;
        $this->assertEquals($this->testModelClass, AppUserGroup::class);

        $this->entrypoint = (new $this->testModelClass)->getTable();
        $this->assertNotNull($this->entrypoint);
    }

    protected function assertModelData($expect, $actual)
    {
        $this->assertIsArray($actual);
        if (array_key_exists('id', $expect)) {
            $this->assertArrayHasKey('id', $actual);
            $this->assertEquals($expect['id'], $actual['id']);
        }
        if (array_key_exists('user_id', $expect)) {
            $this->assertArrayHasKey('user_id', $actual);
            $this->assertIsArray(Arr::get($actual, 'user_id'));
            $this->assertTrue(Arr::has($actual, 'user_id.id'));
            $this->assertEquals($expect['user_id'], Arr::get($actual, 'user_id.id'));
        }
        if (array_key_exists('group_id', $expect)) {
            $this->assertArrayHasKey('group_id', $actual);
            $this->assertIsArray(Arr::get($actual, 'group_id'));
            $this->assertTrue(Arr::has($actual, 'group_id.id'));
            $this->assertEquals($expect['group_id'], Arr::get($actual, 'group_id.id'));
        }
    }
}
