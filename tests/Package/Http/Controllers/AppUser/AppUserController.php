<?php

namespace Antares\Tests\Package\Http\Controllers\AppUser;

use Antares\Crud\CrudController;

class AppUserController extends CrudController
{
    protected $handlerClass = AppUserHandler::class;
}
