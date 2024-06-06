<?php declare(strict_types=1);

namespace Antares\Tests\Unit\Handlers;

use Antares\Tests\Package\AbstractTestCases\HandlerAbstractTestCase;
use Antares\Tests\Package\Http\Controllers\AppUserGroup\AppUserGroupHandler;

class AppUserGroupHandlerTest extends HandlerAbstractTestCase
{
    /** @test */
    public function new_handler()
    {
        $handler = AppUserGroupHandler::make();
        $this->assertInstanceOf(AppUserGroupHandler::class, $handler);
    }

    /** @test */
    public function handler_metadata()
    {
        $handler = AppUserGroupHandler::make();
        $response = $handler->metadata(request());
        $this->assertMetadataStructureFromResponse($response);
    }
}
