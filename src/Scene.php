<?php

namespace Bfg\Layout;

use Illuminate\Contracts\Support\Htmlable;

/**
 * Class Scene
 * @package Bfg\Layout
 */
abstract class Scene implements Htmlable
{
    /**
     * @var array
     */
    protected array $items = [];

    /**
     * @param  string|null  $command
     * @param  mixed  ...$values
     * @return Respond|\Illuminate\Contracts\Foundation\Application|mixed
     */
    public function respond(string $command = null, ...$values)
    {
        return respond($command, ...$values);
    }

    /**
     * @return string|void
     */
    public function toHtml()
    {
        return implode("", $this->items);
    }
}