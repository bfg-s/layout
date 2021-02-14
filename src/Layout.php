<?php

namespace Bfg\Layout;

use Bfg\Layout\Controllers\ContentController;
use Bfg\Layout\Middleware\LayoutMiddleware;
use Bfg\Layout\View\Component;

/**
 * Class Layout
 * @package Bfg\Layout
 */
class Layout
{
    /**
     * @var Component[]
     */
    protected $components = [];

    /**
     * @var array
     */
    protected static array $tgs = [];

    /**
     * @var int
     */
    protected $layout_components = 0;

    /**
     * Layout constructor.
     */
    public function __construct()
    {
        static::$tgs = include __DIR__ . "/tags.php";
    }

    /**
     * Register component in glogal container
     * @param  string  $id
     * @param  Component|Tag  $instance
     * @return array
     */
    public function registerComponent(string $id, Component|Tag $instance)
    {
        if (ContentController::$content_end) {

            $num = 'w' . $this->layout_components++;

        } else {

            $num = count($this->components);
        }

        $this->components[$id][] = $instance;

        //return $id.'#'.$num;
        return [$id, $num];
    }

    /**
     * Check on has component
     * @param  string  $id
     * @return bool
     */
    public function hasComponent(string $id)
    {
        return isset($this->components[$id]);
    }

    public function components()
    {
        return $this->components;
    }

    /**
     * @param  string  $id
     * @return Component
     */
    public function getComponent(string $id)
    {
        return $this->components[$id];
    }

    /**
     * @return array
     */
    public function tags()
    {
        return Layout::$tgs;
    }

    /**
     * Get current layout
     * @return MainLayout|null
     */
    public function current()
    {
        return LayoutMiddleware::$current;
    }
}
