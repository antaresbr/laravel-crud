<?php

namespace Antares\Tests\Package\AbstractTestCases;

use Antares\Tests\Package\Models\AppUser;

abstract class UserCrudAbstractTestCase extends CrudAbstractTestCase
{
    protected function localBootstrap()
    {
        parent::localBootstrap();

        $this->testModelClass = AppUser::class;
        $this->assertEquals($this->testModelClass, AppUser::class);

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
        if (array_key_exists('email', $expect)) {
            $this->assertArrayHasKey('email', $actual);
            $this->assertEquals($expect['email'], $actual['email']);
        }
        if (array_key_exists('email_verified_at', $expect)) {
            $this->assertArrayHasKey('email_verified_at', $actual);
            $this->assertEquals($expect['email_verified_at'], $actual['email_verified_at']);
        }
        if (array_key_exists('remember_token', $expect)) {
            $this->assertArrayHasKey('remember_token', $actual);
            $this->assertEquals($expect['remember_token'], $actual['remember_token']);
        }
        if (array_key_exists('phone', $expect)) {
            $this->assertArrayHasKey('phone', $actual);
            $this->assertEquals($expect['phone'], $actual['phone']);
        }
    }
}
