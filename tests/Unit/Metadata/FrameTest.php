<?php declare(strict_types=1);

namespace Antares\Tests\Unit\Metadata;

use Antares\Crud\Metadata\Frame;
use Antares\Tests\Package\TestCase;
use PHPUnit\Framework\Attributes\Test;

class FrameTest extends TestCase
{
    #[Test]
    public function new_metadata()
    {
        $obj = Frame::make([
            'title' => 'Frame title',
            'size' => 'sm',
            'backdrop' => 'static',
        ]);
        $this->assertInstanceOf(Frame::class, $obj);
        
        $metadata = $obj->toArray();
        $this->assertIsArray($metadata);
    }
}
