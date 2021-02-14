<?php

namespace Bfg\Layout\Core;

/**
 * Trait PartCore
 * @package Bfg\Layout\Core
 */
trait PartCore
{
    /**
     * Is has request callable methods
     * @return bool
     */
    public function isHasRequest()
    {
        return request()->ajax() &&
            request()->has($this->id_hash) &&
            isset($this->methods[request()->get($this->id_hash)]);
    }
}