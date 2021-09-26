<?php

namespace Antares\Crud\Providers;

use Illuminate\Support\ServiceProvider;

class CrudServiceProvider extends ServiceProvider
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
        $this->loadTranslationsFrom(ai_crud_path('lang'), 'crud');
    }
}
