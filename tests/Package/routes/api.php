<?php

use Antares\Crud\Route\CrudRoute;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth')->group(function () {
    CrudRoute::apiResource('users', 'AppUser\AppUserController')->parameters(['users' => 'user']);
    CrudRoute::apiResource('groups', 'AppGroup\AppGroupController')->parameters(['groups' => 'group']);
    CrudRoute::apiResource('user_groups', 'AppUserGroup\AppUserGroupController')->parameters(['user_groups' => 'user_group']);
});
