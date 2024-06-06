<?php declare(strict_types=1);

namespace Antares\Tests\Unit\Models;

use Antares\Tests\Package\Models\AppUserGroup;
use Antares\Tests\Package\TestCase;

class AppUserGroupTest extends TestCase
{
    /** @test */
    public function new_model()
    {
        $model = new AppUserGroup();
        $this->assertInstanceOf(AppUserGroup::class, $model);
    }

    /** @test */
    public function model_properties()
    {
        $model = new AppUserGroup();
        $this->assertEquals('user_groups', $model->getTable());
        $this->assertEquals('id', $model->getKeyName());
    }
}
