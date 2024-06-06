<?php

namespace Antares\Tests\Package\Http\Controllers\AppUser;

use Antares\Crud\CrudHandler;
use Antares\Crud\CrudModel;
use Antares\Tests\Package\Models\AppUser;
use Illuminate\Support\Facades\Hash;

class AppUserHandler extends CrudHandler
{
    /**
     * Make a new handler
     *
     * @return static
     */
    public static function make()
    {
        $instance = new static();
        $instance->model = new AppUser();
        $instance->validator = AppUserValidator::make(['model' => $instance->model]);

        return $instance;
    }

    protected function afterIndex(array &$items)
    {
        foreach ($items as &$item) {
            $item->password = null;
        }
        return true;
    }

    protected function beforeStore(array &$data)
    {
        try {
            if ($this->validateAlreadyUsedEmail($data)) {
                $this->errors()->add('email', 'E-mail already used');
            }

            if (empty($data['password'])) {
                $this->errors()->add('password', "Provide a password");
            }

            if ($this->errors()->isEmpty()) {
                $data['password'] = Hash::make($data['password']);
            }
        } finally {
            return $this->errorResponseOrTrue([
                'error' => [
                    'code' => 1001,
                    'message' => 'store | Data validation error',
                ],
                'action' => 'store',
                'source' => $data,
            ]);
        }
    }

    protected function beforeUpdate(array &$old, array &$delta, CrudModel $model)
    {
        try {
            if ($this->validateAlreadyUsedEmail($delta)) {
                $this->errors()->add('email', 'E-mail já utilizado.');
            }

            $old['password'] = $model['password'];
            if (array_key_exists('password', $delta) and !empty($delta['password'])) {
                //-- get old password to bypass validation
                $delta['password'] = Hash::make($delta['password']);
            }
        } finally {
            return $this->errorResponseOrTrue([
                'error' => [
                    'code' => 1002,
                    'message' => 'update | Data validation error',
                ],
                'action' => 'update',
                'source' => $delta,
            ]);
        }
    }

    private function validateAlreadyUsedEmail($data)
    {
        if (array_key_exists('email', $data) and !empty($data['email'])) {
            return (AppUser::where('email', $data['email'])->count() > 0);
        }
        return false;
    }
}
