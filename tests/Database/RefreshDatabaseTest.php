<?php

namespace Antares\Tests\Database;

use Antares\Tests\Package\TestCase;
use Antares\Tests\TestCase\Models\User;
use Antares\Tests\TestCase\Traits\RefreshDatabaseTrait;

class RefreshDatabaseTest extends TestCase
{
    use RefreshDatabaseTrait;

    /** @test */
    public function refreshed_database()
    {
        //$this->refreshDatabase();
        $this->assertRefreshedDatabase();
    }

    /** @test */
    public function seed_data()
    {
        $this->seedAndTestUsers();
    }

    /** @test */
    public function check_persistency()
    {
        $this->assertCount(0, User::all());
    }
}
