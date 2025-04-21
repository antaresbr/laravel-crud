<?php declare(strict_types=1);

namespace Antares\Tests\Unit\Handlers;

use Antares\Tests\Package\AbstractTestCases\HandlerAbstractTestCase;
use Antares\Tests\Package\Http\Controllers\AppUser\AppUserHandler;
use PHPUnit\Framework\Attributes\Test;

class AppUserHandlerTest extends HandlerAbstractTestCase
{
    #[Test]
    public function new_handler()
    {
        $handler = AppUserHandler::make();
        $this->assertInstanceOf(AppUserHandler::class, $handler);
    }

    #[Test]
    public function handler_metadata()
    {
        $handler = AppUserHandler::make();
        $response = $handler->metadata(request());
        $this->assertMetadataStructureFromResponse($response);
    }
}
