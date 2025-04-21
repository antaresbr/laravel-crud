<?php declare(strict_types=1);

namespace Antares\Tests\Unit\Handlers;

use Antares\Tests\Package\AbstractTestCases\HandlerAbstractTestCase;
use Antares\Tests\Package\Http\Controllers\AppGroup\AppGroupHandler;
use PHPUnit\Framework\Attributes\Test;

class AppGroupHandlerTest extends HandlerAbstractTestCase
{
    #[Test]
    public function new_handler()
    {
        $handler = AppGroupHandler::make();
        $this->assertInstanceOf(AppGroupHandler::class, $handler);
    }

    #[Test]
    public function handler_metadata()
    {
        $handler = AppGroupHandler::make();
        $response = $handler->metadata(request());
        $this->assertMetadataStructureFromResponse($response);
    }
}
