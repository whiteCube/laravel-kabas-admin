<?php 

namespace WhiteCube\Admin;

use Illuminate\Support\ServiceProvider;
use WhiteCube\Admin\Accessors\Page;

class AdminServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes($this->config(), 'config');
        $this->publishes($this->assets(), 'public');
        $this->loadRoutesFrom(__DIR__.'/../routes.php');
        $this->loadViewsFrom(__DIR__.'/../views', 'admin');
    }

    protected function config()
    {
        return [ __DIR__ . '/../config/admin.php' => config_path('kabas-admin.php') ];
    }

    protected function assets()
    {
        return [ __DIR__ . '/../dist' => public_path('vendor/kabas-admin') ];
    }

    public function register()
    {
        $this->app->singleton('admin', function ($app) {
            $config = $app->make('config');
            return new AdminService($config, new FileWorker);
        });
        $this->app->singleton('page', Page::class);
    }

    public function provides()
    {
        return ['admin'];
    }
}
