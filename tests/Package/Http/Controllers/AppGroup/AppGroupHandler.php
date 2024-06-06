<?php

namespace Antares\Tests\Package\Http\Controllers\AppGroup;

use Antares\Crud\CrudHandler;
use Antares\Tests\Package\Models\AppGroup;

class AppGroupHandler extends CrudHandler
{
    /**
     * Make a new handler
     *
     * @return static
     */
    public static function make()
    {
        $instance = new static();
        $instance->model = new AppGroup();
        $instance->validator = AppGroupValidator::make(['model' => $instance->model]);

        return $instance;
    }
}
