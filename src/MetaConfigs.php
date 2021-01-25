<?php

namespace Bfg\Layout;

/**
 * Class MetaConfigs
 * @package Bfg\Layout
 */
class MetaConfigs {

    /**
     * @var array
     */
    protected static $list = [];

    /**
     * @param string $name
     * @param $value
     */
    public static function add(string $name, $value)
    {
        if (is_array($value)) {

            $value = json_encode($value);
        }

        if ($value === true) {
            $value = 'true';
        } else if ($value === false) {
            $value = 'false';
        } else if ($value === null) {
            $value = 'null';
        }

        static::$list[$name] = $value;
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public static function has(string $name)
    {
        return isset(static::$list[$name]);
    }

    /**
     * @param  string  $name
     * @return bool
     */
    public static function remove(string $name)
    {
        if (isset(static::$list[$name])) {

            unset(static::$list[$name]);

            return true;
        }

        return false;
    }

    /**
     * Make defaults configs
     */
    public static function makeDefaults()
    {
        static::add('env', config('app.env'));
        static::add('token', csrf_token());
        static::add('locale', \App::getLocale());

        if ($route = \Route::current()) {

            static::add('uri', $route->uri);
            static::add('route', $route->getName());
        }
    }

    /**
     * @return array
     */
    public static function get()
    {
        static::makeDefaults();

        return static::$list;
    }
}