<?php

namespace Antares\Tests\Package\AbstractTestCases;

use Antares\Tests\Package\Models\AppGroup;

abstract class GroupCrudAbstractTestCase extends CrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->testModelClass = AppGroup::class;
        $this->assertEquals($this->testModelClass, AppGroup::class);

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
        if (array_key_exists('name', $expect)) {
            $this->assertArrayHasKey('name', $actual);
            $this->assertEquals($expect['name'], $actual['name']);
        }
        if (array_key_exists('description', $expect)) {
            $this->assertArrayHasKey('description', $actual);
            $this->assertEquals($expect['description'], $actual['description']);
        }
    }
}
