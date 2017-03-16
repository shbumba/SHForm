<?php
namespace SHForm;

class PrepareInput {
    public static $data = array();

    public static function setData($data)
    {
        self::$data = $data;
    }

    public static function prepare($name, $default = null)
    {
        if (count(self::$data) <= 0) return false;

        return self::prepareData($name, self::$data, $default);
    }

    public static function has($key)
    {
        return self::hasIn($key, self::$data);
    }

    public static function hasIn($key, array $data)
    {
        return Arr::has($data, $key);
    }

    public static function get($key)
    {
        return self::getIn(self::$data, $key);
    }

    public static function getIn($key, array $data)
    {
        return Arr::get($data, $key);
    }

    public static function prepareData($key, array $data, $default = null)
    {
        if (count($data) <= 0) return false;

        if (self::hasIn($key, $data)) {
            $value = self::getIn($key, $data);

            if (is_array($value)) {
                return self::prepareArray($key, $data);
            } else {
                return htmlspecialchars(stripslashes($value));
            }
        }

        return $default;
    }

    public static function prepareArray($key, array $data)
    {
        if (!self::hasIn($key, $data)) return false;

        $value = self::getIn($key, $data);

        if (!is_array($value)) return false;

        $return = array();

        foreach ($value as $dataKey => $dataVal) {
            if (is_array($dataVal)) {
                $return[$dataKey] = self::prepareArray($key . '.' . $dataKey, $dataVal);
            } else {
                $return[$dataKey] = self::prepareData($key . '.' . $dataKey, $data[$key]);
            }
        }

        return $return;
    }
}
