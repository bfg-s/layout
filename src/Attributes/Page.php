<?php

namespace Bfg\Layout\Attributes;

use Bfg\Route\Attributes\Route;

/**
 * Class Page
 * @package Bfg\Layout\Attributes
 */
#[\Attribute]
class Page extends Route
{
    /**
     * Page constructor.
     * @param  string  $uri
     * @param  string|null  $name
     * @param  array|string  $middleware
     * @param  string  $layout
     */
    public function __construct(string $uri = "/", ?string $name = null, array|string $middleware = [], string $layout = "default")
    {
        parent::__construct(
            method: ['GET', 'HEAD', 'POST'],
            uri: $uri,
            name: $name,
            middleware: array_merge([
                'web', "layout:{$layout}"
            ], \Arr::wrap($middleware))
        );
    }
}