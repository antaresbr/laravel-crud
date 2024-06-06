<?php

namespace Antares\Tests\Package\Http\Controllers\AppUser;

use Antares\Crud\CrudValidator;

class AppUserValidator extends CrudValidator
{
    protected $defaultRules = [
        'id'    => 'integer|required|unique',
        'name'  => 'string|required|min:3|max:255',
        'email' => 'string|required',
        'phone' => 'string|nullable',
    ];
}
