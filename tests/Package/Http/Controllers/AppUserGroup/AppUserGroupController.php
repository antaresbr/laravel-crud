<?php

namespace Antares\Tests\Package\Http\Controllers\AppUserGroup;

use Antares\Crud\CrudController;

class AppUserGroupController extends CrudController
{
    protected $handlerClass = AppUserGroupHandler::class;
}
