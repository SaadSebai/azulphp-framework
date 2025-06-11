<?php

namespace App\Core\Helpers;

class Str
{
    public static function toCamelCase(string $string): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
    }

    public static function uppercase(string $string): string
    {
        return strtoupper($string);
    }

    public static function afterLast(string $string, string $separator = '/'): string
    {
        $pos = strrpos($string, $separator);
        if ($pos === false) {
            return $string; // Separator not found, return whole string
        }
        return substr($string, $pos + strlen($separator));
    }

    public static function beforeLast(string $string, string $separator = '/'): string
    {
        $pos = strrpos($string, $separator);
        if ($pos === false) {
            return $string; // Separator not found, return whole string
        }
        return substr($string, 0, $pos);
    }

    public static function pathToNameSpace(string $string): string
    {
        return str_replace('/', '\\', $string);
    }
}