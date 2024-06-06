<?php declare(strict_types=1);

namespace Antares\Tests\Unit\Models;

use Antares\Tests\Package\Models\AppUser;
use Antares\Tests\Package\TestCase;

class AppUserTest extends TestCase
{
    /** @test */
    public function new_model()
    {
        $model = new AppUser();
        $this->assertInstanceOf(AppUser::class, $model);
    }

    /** @test */
    public function model_properties()
    {
        $model = new AppUser();
        $this->assertEquals('users', $model->getTable());
        $this->assertEquals('id', $model->getKeyName());
    }
}
