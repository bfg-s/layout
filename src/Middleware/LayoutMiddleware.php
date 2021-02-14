<?php

namespace Bfg\Layout\Middleware;

use Bfg\Layout\Controllers\CallController;
use Bfg\Layout\Controllers\ContentController;
use Bfg\Layout\MainLayout;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
     * @var array
     */
    static $responces = [];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $custom_layout
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $custom_layout = 'default')
    {
        $ajax = $request->ajax();

        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        $response->header('X-CSRF-TOKEN', csrf_token());

        if (!$ajax && $request->isMethod("get")) {

            $layout = $this->layout_class($custom_layout);

            if ($layout !== false) {

                static::$current = $layout;

                $origin_content = $response->getContent();

                ContentController::$content_end = true;

                $content = static::$current->setContent($origin_content)
                    ->create_body_scripts()->render();

                if ($response->exception || $response instanceof RedirectResponse) {

                    return $response;
                }

                $response->setContent($content);
            }

            else {

                throw new \Exception("Layout Class [{$custom_layout}] is not exists!");
            }

        } else if ($ajax) {

            $componentContent = ComponentMiddleware::componentRequest($response, $request, $response->getContent());

            if ($componentContent !== false) {

                $response->setContent($componentContent);
            }
        }

        return $response;
    }

    /**
     * Get current layout class
     * @param  string  $default
     * @return mixed
     */
    protected function layout_class(string $default): mixed
    {
        $layout = false;

        if ($tc = $this->checkClass($default)) {

            $layout = $tc;
        }

        else if (!class_exists($default)) {

            $layout = "App\\Layouts\\" . ucfirst(Str::camel($default)) . "Layout";
        }

        return $layout && class_exists($layout) ? app($layout) : false;
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
