<?php

namespace Bfg\Layout\Controllers;

use Bfg\Layout\Core\ResourceResponseImitation;
use Bfg\Layout\MainLayout;
use Bfg\Layout\Middleware\LayoutMiddleware;
use Bfg\Layout\Respond;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\ResourceResponse;

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
    public function index(Request $request)
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

                    if ($return['response'] instanceof JsonResource) {

                        if ($return['response'] instanceof ResourceCollection) {

                            $return['response'] = $return['response']->toResponse($request);

                            $result['$response'] = $return['response']->getData(1);
                        }
                        else {

                            $responce = app(
                                ResourceResponseImitation::class,
                                ['resource' => $return['response']]
                            );

                            $result['$response'] = $responce->toResponse($request);
                        }
                    }

                    else {

                        $result['$response'] = $return['response'];
                    }
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