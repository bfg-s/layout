<?php

namespace Bfg\Layout\Core;

use Illuminate\Http\Resources\Json\ResourceResponse;

/**
 * Class ResourceResponseImitation
 * @package Bfg\Layout\Core
 */
class ResourceResponseImitation extends ResourceResponse
{
    /**
     * Create an Bfg layout response.
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toResponse($request)
    {
        return $this->wrap(
            $this->resource->resolve($request),
            $this->resource->with($request),
            $this->resource->additional
        );
    }
}
