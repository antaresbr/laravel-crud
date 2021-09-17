<?php

namespace Antares\Crud\Route;

use Illuminate\Routing\PendingResourceRegistration;

class CrudRoute
{
    /**
     * The default actions for an api resourceful controller.
     *
     * @var array
     */
    protected static $apiResourceDefaults = ['metadata', 'search', 'index', 'show', 'store', 'update', 'destroy'];

    /**
     * Route a resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public static function resource($name, $controller, array $options = [])
    {
        $registrar = new CrudResourceRegistrar(app('router'));

        return new PendingResourceRegistration(
            $registrar,
            $name,
            $controller,
            $options
        );
    }

    /**
     * Register an array of resource controllers.
     *
     * @param  array  $resources
     * @param  array  $options
     * @return void
     */
    public static function resources(array $resources, array $options = [])
    {
        foreach ($resources as $name => $controller) {
            static::resource($name, $controller, $options);
        }
    }

    /**
     * Route an API resource to a controller.
     *
     * @param  string  $name
     * @param  string  $controller
     * @param  array  $options
     * @return \Illuminate\Routing\PendingResourceRegistration
     */
    public static function apiResource($name, $controller, array $options = [])
    {
        $only = static::$apiResourceDefaults;

        if (isset($options['except'])) {
            $only = array_diff($only, (array) $options['except']);
        }

        return static::resource($name, $controller, array_merge([
            'only' => $only,
        ], $options));
    }

    /**
     * Register an array of API resource controllers.
     *
     * @param  array  $resources
     * @param  array  $options
     * @return void
     */
    public static function apiResources(array $resources, array $options = [])
    {
        foreach ($resources as $name => $controller) {
            static::apiResource($name, $controller, $options);
        }
    }
}
