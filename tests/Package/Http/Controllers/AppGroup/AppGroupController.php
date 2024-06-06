<?php

namespace Antares\Tests\Package\Http\Controllers\AppGroup;

use Antares\Crud\CrudController;

class AppGroupController extends CrudController
{
    protected $handlerClass = AppGroupHandler::class;
}
