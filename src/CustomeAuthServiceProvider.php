<?php

namespace Wmtharshp\Authentication;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Auth;

class WmtharshpAuthServiceProvider extends ServiceProvider
{
       /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(StatefulGuard::class, function () {
            return Auth::guard('web');
        });
    }


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/views' => resource_path('views'),
            __DIR__.'/assets' => public_path('assets'),
            __DIR__.'/Http/Controllers' => app_path('/Http/Controllers'),
            __DIR__.'/Http/Requests' => app_path('/Http/Requests'),
            __DIR__.'/Http/Middleware' => app_path('/Http/Middleware'),
            __DIR__.'/seeders' => base_path('/database/seeders'),
            __DIR__.'/routes' => base_path('/routes'),
            __DIR__.'/Composer.php' => base_path('/app/Composer.php'),
        ],'customeauth-view');
        $this->commands([
            Console\InstallCommand::class,
            Console\PublishCommand::class,
        ]);
    }

}