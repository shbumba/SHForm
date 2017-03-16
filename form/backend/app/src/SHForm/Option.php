<?php
namespace SHForm;

class Option
{
    protected static $values = array();
    protected static $replace = array();

    public static function set($key, $value)
    {
        Arr::set(self::$values, $key, $value);
    }

    public static function get($key, $default = null)
    {
        return Arr::get(self::$values, $key, $default);
    }

    public static function has($key)
    {
        return Arr::has(self::$values, $key);
    }

    public static function offsetExists($key)
    {
        return self::has($key);
    }

    public static function offsetGet($key)
    {
        return self::get($key);
    }

    public static function offsetSet($key, $value)
    {
        self::set($key, $value);
    }

    public static function offsetUnset($key)
    {
        self::set($key, null);
    }

    public static function dump()
    {
        return '<?php return ' . var_export(self::$values, true) . ';';
    }

    protected static function merge(array $current, array $new)
    {
        return Arr::merge($current, $new);
    }

    public static function getValues()
    {
        return self::$values;
    }
}