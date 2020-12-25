<?php

namespace Bfg\Layout\Core;

use Illuminate\Contracts\Support\Renderable;

/**
 * Class Tag
 * @package Bfg\Layout\Core
 */
class Tag extends TagCollect implements Renderable {

    /**
     * Element of tag
     * @var string
     */
    protected $e;

    /**
     * Attributes of tag
     * @var array
     */
    protected $a = [];

    /**
     * Contents of tag
     * @var array
     */
    protected $c = [];

    /**
     * Tag constructor.
     * @param  string|null  $e
     * @param  mixed  ...$params
     */
    public function __construct(string $e = null, ...$params)
    {
        if ($e) $this->e = $e;
        $this->when(...$params);
    }

    /**
     * @param  mixed  ...$params
     * @return $this
     */
    public function when(...$params)
    {
        foreach ($params as $param) {

            if (is_callable($param)) {

                call_user_func($param, $this);
            }

            else if (is_array($param)) {

                $this->attr($param);
            }

            else {

                $this->c[] = $param;
            }
        }

        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function appEnd(...$content)
    {
        array_push($this->c, ...$content);

        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function prepEnd(...$content)
    {
        array_unshift($this->c, ...$content);

        return $this;
    }

    /**
     * @param string|array $name
     * @param  mixed|null  $value
     * @return $this
     */
    public function attr($name, $value = null)
    {
        if (is_array($name)) {
            foreach ($name as $k => $v) {
                $this->attr($k, $v);
            }
            return $this;
        }

        if (is_array($value)) $value = json_encode($value);

        if ($name == 'class') {
            if (isset($this->a[$name])) {
                $this->a[$name] .= " " . $value;
            } else {
                $this->a[$name] = $value;
            }
        } else {
            $this->a[$name] = $value;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $close = static::isClosable($this->e);
        $has_attrs = !!count($this->a);
        $a = [];
        foreach ($this->a as $k => $v) {
            $q = strpos($v, '"') === false ? '"' : "'";
            $a[] = "{$k}={$q}{$v}{$q}";
        }
        $attrs = ($has_attrs ? " ":"") . implode(" ", $a);
        $start = "<{$this->e}" . $attrs . ($close ? ">" : "/>");
        $end = $close ? "</{$this->e}>":"";
        return $start . implode("", $this->c) . $end;
    }

    /**
     * @param  string  $element
     * @return bool
     */
    public static function isClosable(string $element)
    {
        $tags = \Layout::tags();

        return isset($tags[$element]) ? !!$tags[$element] : true;
    }

    /**
     * @param $name
     * @param $arguments
     * @return Tag
     */
    public function add($name, $arguments)
    {
        $tag = new Tag($name, ...$arguments);
        $tag->setParent($this);
        $this->appEnd($tag);
        return $tag;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * @var Tag
     */
    protected $parent;

    /**
     * @param  Tag  $tag
     * @return $this
     */
    public function setParent(Tag $tag)
    {
        $this->parent = $tag;

        return $this;
    }

    /**
     * @return $this|Tag
     */
    public function root()
    {
        if ($this->parent) {

            $root1 = $this->parent->root();

            if ($root1 !== $this->parent) {

                return $root1;
            }

            return  $this->parent;
        }

        return $this;
    }
}