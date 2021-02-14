<?php

namespace Bfg\Layout\View;

use Bfg\Dev\Support\Behavior\EmbeddedCall;
use Bfg\Layout\Core\PartCore;
use Bfg\Layout\Tag;

/**
 * Class Part
 * @package Bfg\Layout\View
 */
class Part extends Tag
{
    use PartCore;

    /**
     * Node element name tag
     * @var string
     */
    static string $element = "div";

    /**
     * ID of part (unique id)
     * @var string
     */
    public string $id;

    /**
     * Num of part
     * @var string
     */
    public string $num;

    /**
     * Root component data initialize
     * @var bool
     */
    public bool $root = true;

    /**
     * Response of callable part
     * @var array
     */
    public mixed $response;

    /**
     * If called part method
     * @var bool
     */
    public bool $called = false;

    /**
     * Hash of id
     * @var string
     */
    protected string $id_hash;

    /**
     * Properties (method "prop")
     * @var array
     */
    protected array $properties = [];

    /**
     * Assets (method "asset")
     * @var array
     */
    protected array $assets = [];

    /**
     * Methods (method "method")
     * @var array
     */
    protected array $methods = [];

    /**
     * Part constructor.
     * @param  string  $name
     * @param  mixed  ...$params
     */
    public function __construct(
        protected string $name, ...$params
    ) {
        parent::__construct(
            static::$element, ...$params
        );

        /** Register component and get data */
        list(,$this->num) = \Layout::registerComponent($this->name, $this);

        /** Make component ID */
        $this->id = $this->name . "\\" . $this->num;

        $this->id_hash = base64_encode($this->id);
    }

    /**
     * Merge properties
     * @param  array  $props
     * @return $this
     */
    public function props(array $props)
    {
        $this->properties = array_merge($this->properties, $props);

        return $this;
    }

    /**
     * Merge assets
     * @param  array  $assets
     * @return $this
     */
    public function assets(array $assets)
    {
        $this->assets = array_merge($this->assets, $assets);

        return $this;
    }

    /**
     * Merge methods
     * @param  array  $methods
     * @return $this
     */
    public function methods(array $methods)
    {
        $this->methods = array_merge($this->methods, $methods);

        return $this;
    }

    /**
     * Add slot to part
     * @param $data
     * @param  string  $name
     * @return $this
     */
    public function slot($data, string $name = 'default')
    {
        if ($name == 'default') {
            $this->appEnd((string)$data);
        } else {
            $this->add('span', [['data-sf' => $this->num, 'data-s' => $name], (string)$data]);
        }

        return $this;
    }

    /**
     * Add property to part
     * @param  string  $name
     * @param  mixed|null  $value
     * @return $this
     */
    public function prop(string $name, mixed $value = null)
    {
        $this->properties[$name] = $value;

        return $this;
    }

    /**
     * Add asset to part
     * @param  string  $name
     * @param  mixed|null  $value
     * @return $this
     */
    public function asset(string $name, mixed $value = null)
    {
        $this->assets[$name] = $value;

        return $this;
    }

    /**
     * Add method to part
     * @param  string  $name
     * @param  callable  $callable
     * @return $this
     */
    public function method(string $name, callable $callable)
    {
        $this->methods[$name] = $callable;

        return  $this;
    }

    /**
     * Render a part component
     * @return string
     */
    public function render()
    {
        $this->make_response();

        $this->make_data();

        return parent::render();
    }

    /**
     * Make a response of callable part
     */
    protected function make_response()
    {
        /** Check on out call */
        if ($this->isHasRequest()) {

            $method = request()->get($this->id_hash);

            try {
                $this->response = EmbeddedCall::make($this->methods[$method]);
                $this->called = true;
            } catch (\Throwable $exception) {
                dd($exception);
            }
        }
    }

    /**
     * Make all part data tags
     */
    protected function make_data()
    {
        $this->attr('data-e'.($this->root ? "r" : "c"), $this->id);

        if (count($this->properties)) {

            $this->attr('data-a', $this->properties);
        }

        if (count($this->assets)) {

            $this->attr('data-v', $this->assets);
        }

        if (count($this->methods)) {

            $this->attr('data-m', implode(';', array_keys($this->methods)));
        }
    }
}