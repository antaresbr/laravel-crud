<?php

namespace Antares\Tests\Package\Traits;

use Antares\Tests\Package\Models\AppUser;
use Illuminate\Support\Facades\Auth;

trait AuthUserTrait
{
    protected function authUser($id = 1)
    {
        $user = AppUser::find($id);
        if (!$user) {
            $user = AppUser::all()->first();
        }
        Auth::login($user);
        return $user;
    }
}
