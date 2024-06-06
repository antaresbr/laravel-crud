<?php declare(strict_types=1);

namespace Antares\Tests\Unit\Models;

use Antares\Tests\Package\Models\AppGroup;
use Antares\Tests\Package\TestCase;

class AppGroupTest extends TestCase
{
    /** @test */
    public function new_model()
    {
        $model = new AppGroup();
        $this->assertInstanceOf(AppGroup::class, $model);
    }

    /** @test */
    public function model_properties()
    {
        $model = new AppGroup();
        $this->assertEquals('groups', $model->getTable());
        $this->assertEquals('id', $model->getKeyName());
    }
}
