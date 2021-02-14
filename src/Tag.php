<?php

namespace Bfg\Layout;

use Bfg\Layout\Core\TagCollect;
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
     * Left join data
     * @var string
     */
    protected $lj = "";

    /**
     * Right join data
     * @var string
     */
    protected $rj = "";

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
     * @param  string  $element
     * @return $this
     */
    public function element(string $element)
    {
        $this->e = $element;

        return $this;
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
    public function text(...$content)
    {
        array_push($this->c, implode("", $content));

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
     * @param $content
     * @return $this
     */
    public function prepEndText(...$content)
    {
        array_unshift($this->c, implode("", $content));

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
     * Render a tag component
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
        return $this->lj . $start . implode("", $this->c) . $end . $this->rj;
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

    /**
     * Left join data
     * @param  string  $data
     * @return $this
     */
    public function leftJoin(string $data)
    {
        $this->lj = $data . $this->lj;

        return $this;
    }

    /**
     * Right join data
     * @param  string  $data
     * @return $this
     */
    public function rightJoin(string $data)
    {
        $this->rj .= $data;

        return $this;
    }
}