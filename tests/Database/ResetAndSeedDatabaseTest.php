<?php

namespace Antares\Tests\Database;

use Antares\Tests\Package\TestCase;
use Antares\Tests\TestCase\Models\Group;
use Antares\Tests\TestCase\Models\User;
use Antares\Tests\TestCase\Models\UserGroup;
use Antares\Tests\TestCase\Traits\ResetDatabaseTrait;

class ResetAndSeedDatabaseTest extends TestCase
{
    use ResetDatabaseTrait;

    private $userCount = 30;
    private $groupCount = 5;
    private $userGroupCount = 10;

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
    public function seed_data()
    {
        $this->seedAndTestUsers();
    }
    
    /** @test */
    public function seed_groups()
    {
        $this->seedAndTestGroups();
    }
    
    /** @test */
    public function seed_user_groups()
    {
        $this->seedAndTestUserGroups();
    }

    /** @test */
    public function check_persistency()
    {
        $this->assertCount($this->userCount, User::all());
        $this->assertCount($this->groupCount, Group::all());
        $this->assertCount($this->userGroupCount, UserGroup::all());
    }
}
