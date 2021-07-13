<?php

namespace Bfg\Layout;

use Bfg\Layout\Core\LayoutLivewireComponentsFinder;
use Bfg\Layout\Core\RouteMixin;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Bfg\Layout\Middleware\LayoutMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as ServiceProviderIlluminate;
use Livewire\Commands\ComponentParser;
use Livewire\LivewireComponentsFinder;

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

        $this->app->extend(LivewireComponentsFinder::class, function () {

            $defaultManifestPath = $this->app['livewire']->isRunningServerless()
                ? '/tmp/storage/bootstrap/cache/livewire-components.php'
                : app()->bootstrapPath('cache/livewire-components.php');

            return new LayoutLivewireComponentsFinder(
                new Filesystem,
                config('livewire.manifest_path') ?: $defaultManifestPath,
                ComponentParser::generatePathFromNamespace(
                    config('livewire.class_namespace')
                )
            );
        });
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

