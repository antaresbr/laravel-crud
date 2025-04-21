<?php declare(strict_types=1);

namespace Antares\Tests\Unit\Models;

use Antares\Tests\Package\Models\AppUserGroup;
use Antares\Tests\Package\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AppUserGroupTest extends TestCase
{
    #[Test]
    public function new_model()
    {
        $model = new AppUserGroup();
        $this->assertInstanceOf(AppUserGroup::class, $model);
    }

    #[Test]
    public function model_properties()
    {
        $model = new AppUserGroup();
        $this->assertEquals('user_groups', $model->getTable());
        $this->assertEquals('id', $model->getKeyName());
    }
}
