<?php

namespace Bfg\Layout;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Traits\Macroable;

/**
 * Class Respond
 * @package Bfg\Layout
 */
class Respond implements Arrayable
{
    use Macroable;

    /**
     * Item collection for response call
     * @var array
     */
    protected $items = [];

    /**
     * Put a command
     * @param  string  $command
     * @param  mixed  ...$props
     * @return $this
     */
    public function put(string $command, ...$props)
    {
        $this->items[] = [$command, $props];

        return $this;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->items;
    }

    /**
     * @param  array  $items
     * @return $this
     */
    public function merge(array $items)
    {
        $this->items = array_merge($this->items, $items);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->all();
    }
}