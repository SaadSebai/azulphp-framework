<?php

namespace Azulphp;

use DateTime;

class Validator
{
    const int UNSIGNED_INT_MAX = 4294967295;

    public static function required(string $attribute, mixed $value): ?string
    {
        $message ??= "$attribute is required";

        return $value === null ? $message : null;
    }

    public static function string(string $attribute, mixed $value, int $min = 1, int $max = 255, ?string $message = null): ?string
    {
        if (is_string($value))
        {
            $value = trim($value);

             if (strlen($value) < $min || strlen($value) > $max)
                 return $message ?? "$attribute is out of range.";
        }
        else
        {
            return $message ?? "$attribute is not a valid string.";
        }

        return null;
    }

    public static function email(string $attribute, string $value, ?string $message = null): ?string
    {
        $message ??= "$attribute must be a valid email address.";

        return !filter_var($value, FILTER_VALIDATE_EMAIL)
            ? $message
            : null;
    }

    public static function exists(string $attribute, string $value, string $repository, string $method, ?string $message = null): ?string
    {
        $message ??= "$attribute doesn't exists.";

        return ! (new $repository)->{$method}($value)
            ? $message
            : null;
    }

    public static function phone(string $attribute, string $value, ?string $message = null): ?string
    {
        $message ??= "$attribute must be a valid phone number.";

        $pattern = '/^\+?[1-9]\d{9,14}$/';

        return !preg_match($pattern, $value) ? $message : null;
    }

    public static function int(string $attribute, mixed $value, int $min = 0, int $max = self::UNSIGNED_INT_MAX, ?string $message = null): ?string
    {
        if (filter_var($value, FILTER_VALIDATE_INT) !== false)
        {
            $value = (int) $value;

            if (strlen($value) < $min || strlen($value) > $max)
                return $message ?? "$attribute is out of range.";
        }
        else
        {
            return $message ?? "$attribute is not a valid integer.";
        }

        return null;
    }

    public static function numeric(string $attribute, mixed $value, int $min = 0, int $max = self::UNSIGNED_INT_MAX, ?string $message = null): ?string
    {
        if (is_numeric($value))
        {
            $value = (int) $value;

            if (strlen($value) < $min || strlen($value) > $max)
                return $message ?? "$attribute is out of range.";
        }
        else
        {
            return $message ?? "$attribute is not a valid integer.";
        }

        return null;
    }

    public static function inEnum(string $attribute, string $value, string $enum, ?string $message = null): ?string
    {
        $message ??= "$attribute must be a valid value.";

        return !in_array($value, array_column($enum::cases(), 'value'), true)
            ? $message
            : null;
    }

    public static function date(string $attribute, string $value, string $format = 'Y-m-d', ?string $message = null): ?string
    {
        $message ??= "$attribute must be a valid date.";

        $errors = false;

        if ($date = DateTime::createFromFormat($format, $value)){
            $errors = $date::getLastErrors();
        }

        return !$date || ($errors && array_sum($errors)) || $date->format($format) !== $value
            ? $message
            : null;
    }

    public static function afterDate(string $attribute, string $value, string $beforeAttribute, string $beforeValue, string $format = 'Y-m-d', ?string $message = null): ?string
    {
        $message ??= "$attribute must after $beforeAttribute.";

        $date = DateTime::createFromFormat($format, $value);
        $beforeDate = DateTime::createFromFormat($format, $beforeValue);

        return !$date || !$beforeDate || $beforeDate > $date
            ? $message
            : null;
    }
}
