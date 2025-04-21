<?php

namespace Antares\Tests\Database;

use Antares\Tests\Package\TestCase;
use Antares\Tests\TestCase\Models\Group;
use Antares\Tests\TestCase\Models\User;
use Antares\Tests\TestCase\Models\UserGroup;
use Antares\Tests\TestCase\Traits\ResetDatabaseTrait;
use PHPUnit\Framework\Attributes\Test;

class ResetAndSeedDatabaseTest extends TestCase
{
    use ResetDatabaseTrait;

    private $userCount = 30;
    private $groupCount = 5;
    private $userGroupCount = 10;

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
    public function seed_data()
    {
        $this->seedAndTestUsers();
    }
    
    #[Test]
    public function seed_groups()
    {
        $this->seedAndTestGroups();
    }
    
    #[Test]
    public function seed_user_groups()
    {
        $this->seedAndTestUserGroups();
    }

    #[Test]
    public function check_persistency()
    {
        $this->assertCount($this->userCount, User::all());
        $this->assertCount($this->groupCount, Group::all());
        $this->assertCount($this->userGroupCount, UserGroup::all());
    }
}
