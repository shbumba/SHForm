<?php
namespace SHForm;

class CheckInput {
    public static $error = '';

    private static function setError($error)
    {
        self::$error = $error;
    }

    public static function getError()
    {
        return self::$error;
    }

    public static function email($value = null)
    {
        if (!$value || !filter_var(trim($value), FILTER_VALIDATE_EMAIL)) {
            self::setError('Поле E-Mail заполнено неверно');

            return false;
        }

        return true;
    }

    public static function phone($value = null)
    {
        if (!$value || strlen($value) <= 0 || !preg_match("/^([\+]+)*[0-9\x20\x28\x29\-]{5,20}$/", $value)) {
            self::setError('Телефон не заполнен или имеет неверный формат');

            return false;
        }

        return true;
    }

    public static function string($value = null, $minLength = 1, $maxLength = 255)
    {
        if (!$value || !(strlen($value) >= $minLength && strlen($value) <= $maxLength)) {
            self::setError('Поле {field} должно содержать от "'.$minLength.'" символа до "'.$maxLength.'" символов.');

            return false;
        }

        return true;
    }

    public static function data($value, array $data)
    {
        if (is_array($value)) {
            $compare = array_diff($value, array_keys($data));

            if (!empty($compare)) {
                self::setError('Поле {field} имеет неверное значение.');

                return false;
            }
        } else {
            if (!in_array($value, array_keys($data))) {
                self::setError('Поле {field} имеет неверное значение.');

                return false;
            }
        }

        return true;
    }
}