<?php

namespace Oforge\Engine\Modules\Core\Helper;

/**
 * ArrayHelper
 *
 * @package Oforge\Engine\Modules\Core\Helper
 */
class ArrayHelper {

    /**
     * Prevent instance.
     */
    private function __construct() {
    }

    /**
     * Check when array is associative.
     *
     * @param array $array
     *
     * @return bool
     */
    public static function isAssoc(array $array) : bool {
        return ($array !== array_values($array));
    }

    /**
     * Returns array value or default.
     * Check array key with isset.
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public static function get(array $array, $key, $defaultValue = null) {
        return isset($array[$key]) ? $array[$key] : $defaultValue;
    }

    /**
     * Returns array value or default.
     * Check array key with array_key_exists.
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $defaultValue
     *
     * @return mixed
     */
    public static function getNullable(array $array, $key, $defaultValue = null) {
        return array_key_exists($key, $array) ? $array[$key] : $defaultValue;
    }

    /**
     * Create array with given keys and default value, then merge values of $inputArray
     *
     * @param array $keys
     * @param array $inputArray
     * @param string $defaultValue
     *
     * @return array
     */
    public static function extractArray(array $keys, array $inputArray, $defaultValue = '') : array {
        $tmp = array_fill_keys($keys, $defaultValue);

        return array_replace($tmp, array_intersect_key($inputArray, $tmp));
    }

    /**
     * @param array $array1
     * @param array $array2
     *
     * @return array
     */
    public static function mergeRecursive(array &$array1, array &$array2) {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                if (is_string($key)) {
                    $merged[$key] = is_array($merged[$key]) ? self::mergeRecursive($merged[$key], $value) : $value;
                } else {
                    $merged[] = $value;
                }
            } elseif (is_numeric($key)) {
                if (!in_array($value, $merged)) {
                    $merged[] = $value;
                }
            } else {
                $merged[$key] = $value;
            }
        }
        unset($value);

        return $merged;
    }

}
