<?php declare(strict_types=1);

namespace Antares\Tests\Unit\Models;

use Antares\Tests\Package\Models\AppGroup;
use Antares\Tests\Package\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AppGroupTest extends TestCase
{
    #[Test]
    public function new_model()
    {
        $model = new AppGroup();
        $this->assertInstanceOf(AppGroup::class, $model);
    }

    #[Test]
    public function model_properties()
    {
        $model = new AppGroup();
        $this->assertEquals('groups', $model->getTable());
        $this->assertEquals('id', $model->getKeyName());
    }
}
