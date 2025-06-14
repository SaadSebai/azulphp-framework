<?php

namespace Azulphp\Helpers;

class Arr
{
    /**
     * Get specific keys from array.
     *
     * @param  array  $array
     * @param  array  $keys
     * @return array
     */
    public static function only(array $array, array $keys): array
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }
}