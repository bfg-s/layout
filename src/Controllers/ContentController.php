<?php

namespace Bfg\Layout\Controllers;

use Bfg\Layout\MainLayout;
use Bfg\Layout\MetaConfigs;
use Bfg\Layout\Middleware\LayoutMiddleware;
use Bfg\Layout\Respond;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;

/**
 * Class ContentController
 * @package Bfg\Layout\Controllers
 */
class ContentController
{
    /**
     * A State collection for send
     * @var array
     */
    protected static $state = [];

    /**
     * @var bool|null
     */
    protected static $can;

    /**
     * @var bool|null
     */
    static $content_end = false;

    /**
     * @return array
     */
    public function index($origin_content)
    {
        $result = [
            '$content' => $origin_content,
            '$state' => static::$state,
            '$respond' => app(Respond::class)->toArray(),
            '$configs' => MetaConfigs::get(),
        ];

        return $result;
    }

    /**
     * @param  string  $name
     * @param  array  $data
     */
    public static function toState(string $name, array $data)
    {
        if (static::$can === null) {

            static::$can = is_bfg_cr() || is_bfg_tr();
        }

        if (static::$can && static::$content_end) {

            static::$state[$name] = $data;
        }
    }
}