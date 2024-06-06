<?php

namespace Antares\Tests\Package\Http\Controllers\AppUserGroup;

use Antares\Crud\CrudValidator;

class AppUserGroupValidator extends CrudValidator
{
    protected $defaultRules = [
        'id'          => 'integer|required|unique',
        'user_id'     => 'integer|required',
        'group_id'    => 'integer|required',
    ];
}
