<?php

namespace Bfg\Layout\View;

use Admin\Components\ServicePages\Login\Form;
use Bfg\Dev\EmbeddedCall;
use Bfg\Layout\Controllers\ContentController;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
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
     * Create component in slot by default
     * @var Component
     */
    static protected $slotable;

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
            $text instanceof \Closure ? $text($this) : (string)$text
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
        /** Register component and get data */
        list($name, $num) = \Layout::registerComponent($this->componentName, $this);

        /** Make component ID */
        $id = $name . "\\" . $num;

        /** Push on except bfg component methods */
        array_push(
            $this->except,
            'create', 'attrs', 'attr', 'text', 'slot', 'inner', 'toSlot', 'provoke'
        );

        /** Calling an internal event for feeding content for the api class. */
        $this->inner();

        /** Call request if exists */
        if (request()->ajax()) {

            $rid = base64_encode($id);

            /** Check on out call */
            if (request()->has($rid)) {

                LayoutMiddleware::$responces[$id] =
                    ['response' => EmbeddedCall::make(
                        [$this, request()->get($rid)], [],
                        function (\Throwable $throwable) {
                            dd($throwable);
                        }
                    )];
            }
        }

        /**
         * Make function trap for bfg templating
         * @param  array  $data
         * @return string
         */
        return function (array $data) use ($id, $num) {

            /** Return the parent to the current component. */
            Component::$current = $this->tmp_current;

            /** Transform default component data to bfg templater */
            $roles = __transform_blade_component($data, static::class, $id, $num, !!$this->parent);

            /** Write data variables for response */
            if (isset(LayoutMiddleware::$responces[$id]) && isset($roles['schema']['data-v'])) {

                LayoutMiddleware::$responces[$id]['schema'] = $roles['schema']['data-v'];
            }

            /** Add state fot content getter */
            if (isset($roles['schema']['data-v'])) {

                ContentController::toState($id, $roles['schema']['data-v']);
            }

            /** Return the component as a tag. */
            return tag($this->element, $roles['schema'], (string)$roles['content'])->render();
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
     * Create a callable variable from the given method.
     *
     * Since we do not have a call through a standard type,
     * we can remove the restriction of parameters,
     * since we only determine the type from it.
     *
     * @param  \ReflectionMethod  $method
     * @return mixed
     */
    protected function createVariableFromMethod(\ReflectionMethod $method)
    {
        return $this->createInvokableVariable($method->getName());
    }

    /**
     * Create component (used for api class)
     * @param  array  $params
     * @param  null  $callback
     * @param  string|null  $slotable
     * @return  static|null
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public static function create($params = [], $callback = null, string $slotable = null)
    {
        /** Slotable global require */
        if ($slotable === null) $slotable = static::$slotable;

        /** Slotable mode trap */
        if ($slotable) {

            /**
             * Throw exception if don't have a parent
             */
            if (!Component::$current) {

                throw new \Exception("You can send to the slot only when there is a parent component!");
            }

            /**
             * Make a slot for parent with current component
             */
            Component::$current->slot($slotable, function () use ($params, $callback) {

                /**
                 * Create current component
                 */
                static::create($params, $callback, false);
            });

            return null;
        }

        /** A trap for parameters */
        if (!is_array($params)) { $callback = $params; $params = []; }

        /** Make component instance */
        $component = app('view')->getContainer()->make(static::class, $params);

        /** Start a component */
        app('view')->startComponent($component->resolveView(), $component->data());

        /**
         * If the name of the component is not specified
         * (This happens when a component is created using the api class),
         * generate its name based on api class.
         */
        if (!$component->componentName) {

            $component->withName(__generate_blade_component_name(static::class));
        }

        /** Apply inner callback for api class */
        if ($callback) {

            if ($callback instanceof \Closure) {

                $callback($component);

            } else {

                echo $callback;
            }
        }

        /** If the component is called as a child, then immediately render it. */
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