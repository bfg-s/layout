<?php

namespace Bfg\Layout\Middleware;

use Bfg\Layout\Controllers\CallController;
use Bfg\Layout\MainLayout;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class LayoutMiddleware
 * @package Bfg\Layout\Middleware
 */
class LayoutMiddleware
{
    /**
     * @var MainLayout
     */
    static $current;

    /**
     * @var string
     */
    static $current_action;

    /**
     * @var array
     */
    static $responces = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $layout
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $layout = 'default')
    {
        $ajax = $request->ajax();

        if ((!$ajax && $request->isMethod("get")) || $request->has('bfg')) {

            if ($tc = $this->checkClass($layout)) {

                $layout = $tc;
            }

            else if (!class_exists($layout)) {

                $layout = "App\\Layouts\\" . ucfirst(Str::camel($layout)) . "Layout";
            }

            if (class_exists($layout)) {

                static::$current = app($layout);//new $layout();

                /** @var \Illuminate\Http\Response $response */
                $response = $next($request);

                $response->header('X-CSRF-TOKEN', csrf_token());

                $origin_content = $response->getContent();

                if ($response->exception)  {

                    return $response;
                }

                if (static::$current_action) {

                    $controller = app(CallController::class);

                    $content = $controller->index();
                }

                if (!static::$current_action) {

                    $content = static::$current->setContent($origin_content)
                        ->create_body_scripts()->render();
                }

                $response->setContent($content);

                return $response;
            }

            else {

                throw new \Exception("Layout Class [{$layout}] is not exists!");
            }
        }

        $response = $next($request);

        $response->header('X-CSRF-TOKEN', csrf_token());

        return $response;
    }

    /**
     * Internal override for middleware children
     * @param  string  $sign
     * @return bool
     */
    protected function checkClass(string $sign)
    {
        return false;
    }
}
