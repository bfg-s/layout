<?php

namespace Bfg\Layout\Controllers;

use Bfg\Layout\MainLayout;
use Bfg\Layout\Middleware\LayoutMiddleware;
use Bfg\Layout\Respond;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

/**
 * Class CallController
 * @package Bfg\Layout\Controllers
 */
class CallController
{
    /**
     * @param  Request  $request
     * @param  MainLayout  $content
     * @return mixed
     * @throws \Throwable
     */
    public function index()
    {
        $result = [];

        foreach (LayoutMiddleware::$responces as $var => $return) {

            if ($return !== null) {

                if ($result instanceof Htmlable) {

                    $result = $result->toHtml();

                } else if ($result instanceof Renderable) {

                    $result = $result->render();
                }

                if (isset($return['response']) && $return['response'] !== null) {

                    $result['$response'] = $return['response'];
                }

                if (isset($return['schema'])) {

                    $result['$schema'] = $return['schema'];
                }
            }
        }

        $result['$respond'] = app(Respond::class)->toArray();

        return $result;
    }
}