<?php declare(strict_types=1);

namespace Antares\Tests\Unit\Models;

use Antares\Tests\Package\Models\AppUser;
use Antares\Tests\Package\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AppUserTest extends TestCase
{
    #[Test]
    public function new_model()
    {
        $model = new AppUser();
        $this->assertInstanceOf(AppUser::class, $model);
    }

    #[Test]
    public function model_properties()
    {
        $model = new AppUser();
        $this->assertEquals('users', $model->getTable());
        $this->assertEquals('id', $model->getKeyName());
    }
}
