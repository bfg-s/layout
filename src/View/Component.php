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
abstract class Component extends BladeComponent
{

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
     * @param  \Closure|string  $data
     * @return $this
     */
    public function slot(string $name, $data)
    {
        app('view')->slot($name);

        echo($data instanceof \Closure ? $data() : (string) $data);

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
        $text instanceof \Closure ? $text() : (string) $text
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
            'create', 'attrs', 'attr', 'text', 'slot', 'inner', 'toSlot', 'provoke', 'generateName'
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
            return tag($this->element, $attributes, (string) $roles['content'])->render();
        };
    }

    /**
     * Call inner builder (used for api class)
     */
    public function inner()
    {
    }

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
        if (!is_array($params)) {
            $callback = $params;
            $params = [];
        }

        /**
         * Make component instance
         */
        $component = app('view')->getContainer()->make(static::class, $params);

        /**
         * If the name of the component is not specified
         * (This happens when a component is created using the api class),
         * generate its name based on api class.
         */
        if (!$component->componentName) {
            $component->withName(static::generateName(get_class($component)));
        }

        /** Start a component */
        app('view')->startComponent($component->resolveView(), $component->data());

        /** Apply inner callback for api class */
        if ($callback) {
            if ($callback instanceof \Closure) {
                $callback($component);
            } else {
                echo $callback;
            }
        }

        /** Calling an internal event for feeding content for the api class. */
        $result = $component->inner();

        if (is_array($result)) {
            foreach ($result as $item) {
                $item::create();
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

    /**
     * @param  string|null  $class
     * @return string|null
     */
    public static function generateName(string $class = null)
    {
        $classComponentNamespaces = app(BladeCompiler::class)->getClassComponentNamespaces();

        foreach ($classComponentNamespaces as $alias => $classComponentNamespace) {
            if (\Str::is($classComponentNamespace."*", $class)) {
                $name = implode('.',
                    array_map('Str::camel',
                        array_map('Str::snake',
                            explode('\\',
                                str_replace("{$classComponentNamespace}\\", '', $class)
                            )
                        )
                    )
                );

                return "{$alias}::{$name}";
            }
        }

        return null;
    }

    /**
     * Make and render part component
     * @param  Part  $part
     * @param  array  $data
     * @return string
     */
    protected function makePart(Part $part, array $data)
    {
        $part->root = !$this->parent;

        if (isset($data['__laravel_slots'])) {
            foreach ($data['__laravel_slots'] as $slot_key => $item) {
                $part->slot($item->tohtml(), $slot_key == '__default' ? 'default' : $slot_key);
            }
        }

        if (isset($data['attributes'])) {
            if (isset($data['attributes']['class'])) {
                $part->attr('class', $data['attributes']['class']);
            }

            $part->props($data['attributes']);
        }

        if (isset($data['methods'])) {
            $part->methods($data['methods']);
        }

        if (isset($data['props'])) {
            $part->assets($data['props']);

            if ($part->called) {
                LayoutMiddleware::$responces[$part->id]['schema'] = $data['props'];

                ContentController::toState($part->id, $data['props']);
            }
        }

        return $part->render();
    }

    /**
     * Extract the public properties for the component.
     *
     * @return array
     */
    protected function extractPublicProperties()
    {
        $class = get_class($this);

        if (!isset(static::$propertyCache[$class])) {
            $reflection = new ReflectionClass($this);

            static::$propertyCache[$class] = collect($reflection->getProperties(ReflectionProperty::IS_PUBLIC))
                ->reject(function (ReflectionProperty $property) {
                    return $property->isStatic();
                })
                ->reject(function (ReflectionProperty $property) {
                    return $this->shouldIgnore($property->getName());
                })
                ->map(function (ReflectionProperty $property) {
                    return $property->getName();
                })->all();
        }

        $values = [
            'componentName' => null,
            'attributes' => null,
            'props' => []
        ];

        foreach (static::$propertyCache[$class] as $property) {
            if ($property == 'componentName') {
                $values[$property] = $this->{$property};
            } else {
                if ($property == 'attributes') {
                    $values[$property] = $this->{$property}->getAttributes();
                } else {
                    $values['props'][$property] = $this->{$property};
                }
            }
        }

        return $values;
    }

    /**
     * Extract the public methods for the component.
     *
     * @return array
     */
    protected function extractPublicMethods()
    {
        $class = get_class($this);

        if (!isset(static::$methodCache[$class])) {
            $reflection = new ReflectionClass($this);

            static::$methodCache[$class] = collect($reflection->getMethods(ReflectionMethod::IS_PUBLIC))
                ->reject(function (ReflectionMethod $method) {
                    return $this->shouldIgnore($method->getName());
                })
                ->map(function (ReflectionMethod $method) {
                    return $method->getName();
                });
        }

        $values = [];

        foreach (static::$methodCache[$class] as $method) {
            $values[$method] = [$this, $method];
        }

        return $values;
    }

    /**
     * @param  mixed  ...$arguments
     * @return $this|null
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __invoke(...$arguments)
    {
        return static::create(...$arguments);
    }
}
