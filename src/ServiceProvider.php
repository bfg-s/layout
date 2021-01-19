<?php

namespace Bfg\Layout;

use Bfg\Layout\Core\RouteMixin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Bfg\Layout\Middleware\LayoutMiddleware;
use Illuminate\Support\ServiceProvider as ServiceProviderIlluminate;

/**
 * Class ServiceProvider
 * @package Bfg\Layout
 */
class ServiceProvider extends ServiceProviderIlluminate
{
    /**
     * @var array
     */
    protected $commands = [

    ];

    /**
     * The application's route middleware.
     * @var array
     */
    protected $routeMiddleware = [
        'layout' => LayoutMiddleware::class
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     * @throws \Exception
     */
    public function boot()
    {
        Blade::componentNamespace('App\\Components', 'app');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        Router::mixin(new RouteMixin);

        $this->registerRouteMiddleware();

        $this->commands($this->commands);
    }

    /**
     * Register the route middleware.
     *
     * @return void
     */
    protected function registerRouteMiddleware()
    {
        // register route middleware.
        foreach ($this->routeMiddleware as $key => $middleware) {

            app('router')->aliasMiddleware($key, $middleware);
        }
    }
}

