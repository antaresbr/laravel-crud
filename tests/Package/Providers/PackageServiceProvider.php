<?php

namespace Antares\Tests\Package\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFile(ai_package_path('config/package.php'), 'package');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrations();
        $this->loadRoutes();
    }

    protected function mergeConfigFile($file, $name)
    {
        if (is_file($file) and !Config::has($name)) {
            $this->mergeConfigFrom($file, $name);
        }
    }

    protected function loadMigrations()
    {
        $this->loadMigrationsFrom([
            ai_package_path('Database/migrations'),
        ]);
    }

    protected function loadRoutes()
    {
        $attributes = [
            'prefix' => config('package.route.prefix.api'),
            'namespace' => 'Antares\Tests\Package\Http\Controllers',
        ];
        Route::group($attributes, function () {
            $this->loadRoutesFrom(ai_package_path('routes/api.php'));
        });
    }
}
