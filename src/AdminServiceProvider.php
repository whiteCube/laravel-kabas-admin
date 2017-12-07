<?php 

namespace WhiteCube\Admin;

use Illuminate\Support\ServiceProvider;

class AdminServiceProvider extends ServiceProvider {

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/admin.php' => config_path('kabas-admin.php')
        ], 'config');
        $this->publishes([
            __DIR__ . '/../dist' => public_path('vendor/kabas-admin')
        ], 'public');
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
        $this->loadViewsFrom(__DIR__.'/../views', 'admin');
    }

    public function register()
    {
        $this->app->singleton('admin', function($app) {
            $config = $app->make('config');
            return new AdminService($config, new FileWorker);
        });
    }

    public function provides()
    {
        return ['admin'];
    }

}