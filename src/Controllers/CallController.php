<?php

namespace Bfg\Layout\Controllers;

use Bfg\Layout\MainLayout;
use Bfg\Layout\Middleware\LayoutMiddleware;
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
    public function index(Request $request, MainLayout $content)
    {
        $content->render();
        $result = [
            '$schema' => collect($content->get_page_data())->map(function ($item) {
                return [
                    'v' => $item['v']
                ];
            })
        ];

        foreach (LayoutMiddleware::$responces as $var => $return) {

            if ($return !== null) {

                if ($result instanceof Htmlable) {

                    $result = $result->toHtml();

                } else if ($result instanceof Renderable) {

                    $result = $result->render();
                }

                $result['$response'] = $return;
            }
        }

        //dump(LayoutMiddleware::$responces);

        return $result;
    }
}