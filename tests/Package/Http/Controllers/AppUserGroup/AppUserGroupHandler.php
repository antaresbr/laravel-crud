<?php

namespace Antares\Tests\Package\Http\Controllers\AppUserGroup;

use Antares\Crud\CrudHandler;
use Antares\Tests\Package\Models\AppUserGroup;

class AppUserGroupHandler extends CrudHandler
{
    /**
     * Make a new handler
     *
     * @return static
     */
    public static function make()
    {
        $instance = new static();
        $instance->model = new AppUserGroup();
        $instance->validator = AppUserGroupValidator::make(['model' => $instance->model]);

        return $instance;
    }
}
