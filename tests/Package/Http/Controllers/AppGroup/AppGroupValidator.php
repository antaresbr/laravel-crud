<?php

namespace Antares\Tests\Package\Http\Controllers\AppGroup;

use Antares\Crud\CrudValidator;

class AppGroupValidator extends CrudValidator
{
    protected $defaultRules = [
        'id'          => 'integer|required|unique',
        'name'        => 'string|required|min:3|max:255',
        'description' => 'string|required',
    ];
}
