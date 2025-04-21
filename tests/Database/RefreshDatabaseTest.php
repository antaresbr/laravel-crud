<?php

namespace Antares\Tests\Database;

use Antares\Tests\Package\TestCase;
use Antares\Tests\TestCase\Models\User;
use Antares\Tests\TestCase\Traits\RefreshDatabaseTrait;
use PHPUnit\Framework\Attributes\Test;

class RefreshDatabaseTest extends TestCase
{
    use RefreshDatabaseTrait;

    #[Test]
    public function refreshed_database()
    {
        //$this->refreshDatabase();
        $this->assertRefreshedDatabase();
    }

    #[Test]
    public function seed_data()
    {
        $this->seedAndTestUsers();
    }

    #[Test]
    public function check_persistency()
    {
        $this->assertCount(0, User::all());
    }
}
