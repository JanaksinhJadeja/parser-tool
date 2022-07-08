<?php

declare(strict_types=1);

namespace App\Util;

/**
 * Primary purpose of Util class to provide general utility methods.
 */
class Util
{
    /**
     * Make multi dimension array to key => value format.
     *
     * @param array $array
     * @param string $prefix
     * @return array
     */
    public static function makeArrayFlat(array $array, string $prefix=''): array
    {
        $result = array();
        foreach ($array as $key => $value) {
            $newKey = $prefix . (empty($prefix) ? '' : '.') . $key;
            if (is_array($value) && count($value)) {
                $result = array_merge($result, self::makeArrayFlat($value, $newKey));
            } elseif (is_array($value) && count($value) == 0) {
                $result[$newKey] = '';
            } else {
                $result[$newKey] = $value;
            }
        }
        return $result;
    }
}
