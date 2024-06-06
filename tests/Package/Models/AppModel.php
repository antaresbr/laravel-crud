<?php

namespace Antares\Tests\Package\Models;

use Antares\Crud\CrudModel;

class AppModel extends CrudModel
{
    public function getApi()
    {
        return config('package.route.prefix.api').'/'.$this->table;
    }
}
