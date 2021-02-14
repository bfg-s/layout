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
 * Class ComponentMiddleware
 * @package Bfg\Layout\Middleware
 */
class ComponentMiddleware
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
     * @param  string  $layout
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $layout = 'default')
    {
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        $componentContent = static::componentRequest($response, $request, $response->getContent());

        if ($componentContent !== false) {

            $response->setContent($componentContent);
        }

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

    /**
     * @param  Response  $response
     * @param  Request  $request
     * @param $origin_content
     * @return false|mixed
     */
    public static function componentRequest(Response $response, Request $request, $origin_content)
    {
        $ajax = $request->ajax();

        $content = false;

        if ($ajax && is_bfg_tr()) {

            $content = embedded_call(
                [CallController::class, 'index']
            );

        } else if ($ajax && is_bfg_cr()) {

            $response->header('BFG-CONTENT-RESPONSE', 'true');

            $content = embedded_call(
                [ContentController::class, 'index'], [$origin_content]
            );

        }

        return $content;
    }
}
