<?php

use Illuminate\View\Compilers\ComponentTagCompiler;

if (! function_exists('respond')) {

    /**
     * @param  string|null  $command
     * @param  mixed  ...$values
     * @return \Bfg\Layout\Respond|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function respond (string $command = null, ...$values) {

        if ($command) {

            return app(\Bfg\Layout\Respond::class)->put($command, ...$values);
        }

        return app(\Bfg\Layout\Respond::class);
    }
}

if (! function_exists('tag')) {
    /**
     * @param  string  $tag
     * @param  mixed  ...$params
     * @return \Bfg\Layout\Tag
     */
    function tag (string $tag = null, ...$params) {

        return new \Bfg\Layout\Tag($tag, ...$params);
    }
}

if (! function_exists('part')) {

    /**
     * A part maker
     * @param  string  $component
     * @param  mixed  ...$params
     * @return \Bfg\Layout\View\Part
     */
    function part (string $component, ...$params) {

        return new \Bfg\Layout\View\Part($component, ...$params);
    }
}

if (! function_exists('is_bfg_cr')) {

    /**
     * Check is have BFG Content Request
     * @return bool
     */
    function is_bfg_cr () {

        return request()->headers->get('BFG-CONTENT-REQUEST') == true;
    }
}

if (! function_exists('is_bfg_tr')) {

    /**
     * Check is have BFG Template Request
     * @return bool
     */
    function is_bfg_tr () {

        return request()->headers->get('BFG-TEMPLATE-REQUEST') == true;
    }
}


if (! function_exists('blade_component_class')) {

    /**
     * @param  string  $name
     * @return string|null
     */
    function blade_component_class (string $name) {

        $compiler = app(\Illuminate\View\Compilers\BladeCompiler::class);

        return (new ComponentTagCompiler(
            $compiler->getClassComponentAliases(), $compiler->getClassComponentNamespaces(), $compiler
        ))->componentClass($name);
    }
}