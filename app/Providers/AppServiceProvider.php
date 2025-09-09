<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use View;

class AppServiceProvider extends ServiceProvider
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
        Paginator::useBootstrap();
        //Link with public folder
        View::share('publicDir', asset('public'));
        View::share('viewDir', asset('resources/views'));

        //Helpers
        View::share('Query', '\App\Helpers\Query');
        View::share('Model', function($modelName){
            $modelPath = '\App\Models' . '\\' . $modelName;
            return $modelPath;
        });
        View::share('ButtonSet', '\App\Helpers\ButtonSet');
        View::share('NavMenu', '\App\Helpers\NavMenu');
        View::share('ApiCollection', '\App\Helpers\ApiCollection');
        View::share('Component', '\App\Helpers\Component');
        View::share('PpiSpiPermission', '\App\Helpers\Warehouse\PpiSpiPermission');
    }
}
