<?php

namespace Bfg\Layout;

use Bfg\Layout\View\Component;

/**
 * Class Layout
 * @package Bfg\Layout
 */
class Layout
{
    /**
     * @var Component[][]
     */
    protected $components = [];

    /**
     * @var array
     */
    protected static array $tgs = [];

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
     * @param  Component  $instance
     * @return array
     */
    public function registerComponent(string $id, Component $instance)
    {
        $num = isset($this->components[$id]) ? count($this->components[$id]) : 0;

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
}
