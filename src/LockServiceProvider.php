<?php

namespace Zhchenxin\Lock;

use Illuminate\Support\ServiceProvider;

class LockServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(LockTool::class, function($app) {
            if (empty($app['config']['lock'])) {
                $app['config']['lock'] = require __DIR__ . '/../config/lock.php';
            }
            $config = $app['config']['lock'];
            $name = $config['default'];
            return new LockTool($name, $config[$name]);
        });
    }

    public function provides()
    {
        return [
            LockTool::class,
        ];
    }
}