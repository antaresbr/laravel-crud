<?php

namespace Antares\Crud\Providers;

use Illuminate\Support\ServiceProvider;

class CrudConsoleServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishResources();
        }
    }

    protected function publishResources()
    {
        $this->publishes([
            ai_crud_path('lang') => resource_path('lang/vendor/crud'),
        ], 'crud-lang');
    }
}
