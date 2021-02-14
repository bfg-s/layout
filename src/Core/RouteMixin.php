<?php

namespace Bfg\Layout\Core;

use Bfg\Layout\Middleware\LayoutMiddleware;
use Illuminate\Routing\Router;

/**
 * Class RouteMixin
 * @package Bfg\Layout\Core
 * @mixin Router
 */
class RouteMixin
{
    /**
     * Layout route group creator
     * @return \Closure
     */
    public function layout()
    {
        return function ($routes, string $layout = 'default') {

            $this->group(['middleware' => ["web", "layout:{$layout}"]], $routes);
        };
    }

    /**
     * Page route
     * @return \Closure
     */
    public function page()
    {
        return function ($uri, $action = null) {

            return $this->addRoute(['GET', 'HEAD', 'POST'], $uri, $action);
        };
    }
}