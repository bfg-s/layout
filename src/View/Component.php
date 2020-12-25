<?php

namespace Bfg\Layout\View;

use Bfg\Dev\EmbeddedCall;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\View\ComponentAttributeBag;
use Bfg\Layout\Middleware\LayoutMiddleware;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\Component as BladeComponent;

/**
 * Bfg Class Component
 * @package Bfg\Layout\View
 */
abstract class Component extends BladeComponent {

    /**
     * Provocation attribute
     * To determine the state of an external component.
     * @var array
     */
    public $_pn = [];

    /**
     * Current component for parent setter
     * @var Component
     */
    static $current;

    /**
     * Node name of component tag
     * @var string
     */
    protected $element = "div";

    /**
     * Parent of current component
     * @var Component
     */
    protected $parent;

    /**
     * Temp current component
     * @var Component
     */
    protected $tmp_current;

    /**
     * Set data to the slot of the current component (for api class)
     * @param  string  $name
     * @param  \Closure|string $data
     * @return $this
     */
    public function slot(string $name, $data)
    {
        app('view')->slot($name);

        echo($data instanceof \Closure ? $data() : (string)$data);

        app('view')->endSlot();

        return $this;
    }

    /**
     * Set attribute to the current component (for api class)
     * @param  string  $name
     * @param $value
     * @return $this
     */
    public function attr(string $name, $value)
    {
        return $this->attrs([$name => $value]);
    }

    /**
     * Set an array of attributes to the current component (for api class)
     * @param  array  $attributes
     * @param  bool  $escape
     * @return $this
     */
    public function attrs(array $attributes)
    {
        $this->attributes = $this->attributes ?: new ComponentAttributeBag;

        $attributes['attributes'] = $this->attributes;

        $this->withAttributes($attributes);

        return $this;
    }

    /**
     * Display text or render components inside the current component.
     * @param  Renderable|\Closure|string  $text
     * @return $this
     */
    public function text($text)
    {
        /**
         * Check on rendered and echo
         */
        echo $text instanceof Renderable ? $text->render() : (
            $text instanceof \Closure ? $text() : (string)$text
        );

        return $this;
    }

    /**
     * Determine the states of the external component.
     * @param  string  $path
     * @param $value
     * @return $this
     */
    public function provoke(string $path, ...$value)
    {
        $this->_pn[$path] = $value;

        return $this;
    }

    /**
     * Get the view / view contents that represent the component.
     * @return \Closure|Htmlable|\Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        /**
         * Register component and get id
         */
        $id = \Layout::registerComponent($this->componentName, $this);

        /**
         * Push on except bfg component methods
         */
        array_push(
            $this->except,
            'create', 'attrs', 'attr', 'text', 'slot', 'inner', 'toSlot', 'provoke'
        );

        /**
         * Calling an internal event for feeding content for the api class.
         */
        $this->inner();

        /**
         * Check on out call
         */
        if (request()->has($id)) {

            LayoutMiddleware::$responces[$id] =
                EmbeddedCall::make([$this, request()->get($id)]);
        }

        /**
         * Get class for default state
         */
        $class = isset($this->attributes['class']) ? $this->attributes['class'] : null;

        /**
         * Make function trap for bfg templating
         * @param  array  $data
         * @return string
         */
        return function (array $data) use ($class, $id) {

            /**
             * Return the parent to the current component.
             */
            Component::$current = $this->tmp_current;

            /**
             * Transform default component data to bfg templater
             */
            $roles = __transform_blade_component($data, static::class);

            /**
             * Attribute list
             */
            $attributes = [];

            /**
             * Create to separate data from tags if a bfg layout is used.
             */
            if (LayoutMiddleware::$current) {
                $attributes["data-schema".($this->parent ? "-child" : "")."-id"] = $id;
                if (!request()->ajax()) {
                    LayoutMiddleware::$current->addPageData($roles['schema']['e'], $roles['schema']['m']);
                    $roles['schema']['m'] = [];
                }
                $roles['schema']['id'] = $id;
                LayoutMiddleware::$current->addPageData($id, $roles['schema']);
            } else {
                $attributes["data-schema".($this->parent ? "-child" : "")] = base64_encode(json_encode($roles['schema']));
            }

            if (isset($roles['schema']['a']['class']) || $class) {

                /**
                 * Pass the class to the default node component, if it exists.
                 */
                $attributes['class'] = $roles['schema']['a']['class'] ?? $class;
            }

            /**
             * Return the component as a tag.
             */
            return tag($this->element, $attributes, (string)$roles['content'])->render();
        };
    }

    /**
     * Call inner builder (used for api class)
     */
    public function inner() {}

    /**
     * Resolve the Blade view or view file that should be used when rendering the component.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\Support\Htmlable|\Closure|string
     */
    public function resolveView()
    {
        /**
         * Save current parent
         */
        $this->tmp_current = Component::$current;

        /**
         * Set current componentn how current for childs
         */
        Component::$current = $this;

        /**
         * Set component parent
         */
        $this->parent = $this->tmp_current;

        /**
         * Call default view resolve
         */
        return parent::resolveView();
    }

    /**
     * Create component (used for api class)
     * @param  \Closure|array  $params
     * @param  \Closure|null  $callback
     * @return  static|null
     * @throws  \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function create($params = [], $callback = null)
    {
        /**
         * A trap for parameters
         */
        if (!is_array($params)) { $callback = $params; $params = []; }

        /**
         * Make component instance
         */
        $component = app('view')->getContainer()->make(static::class, $params);
        //$component = new static(...$params);

        /**
         * Start a component
         */
        app('view')->startComponent($component->resolveView(), $component->data());

        /**
         * If the name of the component is not specified
         * (This happens when a component is created using the api class),
         * generate its name based on api class.
         */
        if (!$component->componentName) {

            $component->withName(__generate_blade_component_name(static::class));

            //dd($component->componentName, __generate_blade_component_name(static::class));
        }

        /**
         * Apply inner callback for api class
         */
        if ($callback) {

            if ($callback instanceof \Closure) {

                $callback($component);

            } else {

                echo $callback;
            }
        }

        /**
         * If the component is called as a child, then immediately render it.
         */
        if ($component->tmp_current) {

            echo $component;

            return null;
        }

        return $component;
    }

    /**
     * Create a component for the specified slot.
     * @param  string  $slot
     * @param  \Closure|array  $params
     * @param  \Closure|null  $callback
     * @return  static|null
     * @throws  \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function toSlot(string $slot, $params = [], \Closure $callback = null)
    {
        /**
         * Throw exception if don't have a parent
         */
        if (!Component::$current) {

            throw new \Exception("You can send to the slot only when there is a parent component!");
        }

        /**
         * Make a slot for parent with current component
         */
        return Component::$current->slot($slot, function () use ($params, $callback) {

            /**
             * Create current component
             */
            static::create($params, $callback);
        });
    }

    /**
     * Render component to string
     * @return string
     */
    public function __toString()
    {
        return app('view')->renderComponent();
    }
}