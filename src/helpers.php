<?php

use Illuminate\View\Compilers\ComponentTagCompiler;

if (! function_exists('tag')) {
    /**
     * @param  string  $tag
     * @param  mixed  ...$params
     * @return \Bfg\Layout\Tag
     */
    function tag (string $tag, ...$params) {

        return new \Bfg\Layout\Tag($tag, ...$params);
    }
}

if (! function_exists('__transform_blade_component')) {
    /**
     * @param  array  $data
     * @param  string  $class
     * @param  string  $id
     * @param  bool  $has_parent
     * @return array
     */
    function __transform_blade_component (array $data, string $class, string $id, bool $has_parent = false) {

        $identify = 'data-e'.($has_parent ? "c" : "r");

        $result = [
            $identify => null
            //'e' => null, // Element name
            //'a' => null, // Attributes
            //'c' => [],   // Contents
            //'v' => null, // Variables
            //'m' => [],   // Methods
        ];

        $content = "";

        if (isset($data['__laravel_slots'])) {

            if (isset($data['__laravel_slots']['__default'])) {
                $content = $data['__laravel_slots']['__default']->toHtml();
                unset($data['__laravel_slots']['__default'], $data['slot']);
            }

            foreach ($data['__laravel_slots'] as $slot_key => $item) {
                /** @var \Illuminate\Support\HtmlString $item */
                if ($item) {
                    $result['data-c'][$slot_key] = $item->toHtml();
                }
                unset($data[$slot_key]);
            }

            unset($data['__laravel_slots']);
        }

        foreach ($data as $key => $datum) {

            if ($key === 'attributes') {
                /** @var \Illuminate\View\ComponentAttributeBag $datum */
                foreach ($datum->getAttributes() as $name => $attribute) {
                    if ($name == 'class') { $result[$name] = $attribute;}
                    else { $result['data-a'][$name] = $attribute; }
                }
            } else if ($key === 'componentName') {
                $result[$identify] = $id;
            } else if ($datum instanceof \Illuminate\View\InvokableComponentVariable) {
                $result['data-m'][] = $key;
            } else {
                if ($key == '_pn') {
                    if (is_array($datum) && count($datum)) $result['data-v'][$key] = $datum;
                } else {
                    $result['data-v'][$key] = $datum;
                }
            }
        }

        return ['schema' => $result, 'content' => $content];
    }
}

if (! function_exists('__generate_blade_component_name')) {

    /**
     * @param  string  $class
     * @return string|null
     */
    function __generate_blade_component_name (string $class) {

        $classComponentNamespaces = app(Illuminate\View\Compilers\BladeCompiler::class)->getClassComponentNamespaces();

        foreach ($classComponentNamespaces as $alias => $classComponentNamespace) {

            if (\Str::is($classComponentNamespace . "*", $class)) {

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
}


if (! function_exists('__find_blade_component')) {

    /**
     * @param  string  $name
     * @return string|null
     */
    function __find_blade_component (string $name) {

        $compiler = app(\Illuminate\View\Compilers\BladeCompiler::class);

        return (new ComponentTagCompiler(
            $compiler->getClassComponentAliases(), $compiler->getClassComponentNamespaces(), $compiler
        ))->componentClass($name);
    }
}