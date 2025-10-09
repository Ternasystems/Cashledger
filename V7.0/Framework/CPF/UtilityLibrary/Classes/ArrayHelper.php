<?php

declare(strict_types=1);

namespace TS_Utility\Helpers;

use TS_Configuration\Classes\AbstractCls;

/**
 * A collection of static helper methods for array manipulation.
 * This class cannot be instantiated.
 */
final class ArrayHelper extends AbstractCls
{
    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Safely gets a value from a nested array using "dot" notation.
     *
     * @param array $array The array to search within.
     * @param string $key The dot-separated key (e.g., 'user.profile.name').
     * @param mixed|null $default The default value to return if the key is not found.
     * @return mixed The found value or the default.
     */
    public static function get(array $array, string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }
        if (!str_contains($key, '.')) {
            return $array[$key] ?? $default;
        }
        foreach (explode('.', $key) as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }
        return $array;
    }

    /**
     * Sets a value in a nested array using "dot" notation.
     *
     * @param array &$array The array to modify.
     * @param string $key The dot-separated key (e.g., 'user.profile.name').
     * @param mixed $value The value to set.
     */
    public static function set(array &$array, string $key, mixed $value): void
    {
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key]) || !is_array($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        $array[array_shift($keys)] = $value;
    }

    /**
     * Deletes a value from a nested array using "dot" notation.
     *
     * @param array &$array The array to modify.
     * @param string $key The dot-separated key to delete.
     */
    public static function delete(array &$array, string $key): void
    {
        $keys = explode('.', $key);
        while (count($keys) > 1) {
            $key = array_shift($keys);
            if (!isset($array[$key]) || !is_array($array[$key])) {
                // Key path does not exist, so nothing to delete.
                return;
            }
            $array = &$array[$key];
        }
        unset($array[array_shift($keys)]);
    }

    /**
     * "Plucks" a list of values from an array of arrays/objects.
     *
     * @param array $array An array of associative arrays (e.g., a database result set).
     * @param string $key The key to pluck from each sub-array.
     * @return array A simple array of the plucked values.
     */
    public static function pluck(array $array, string $key): array
    {
        return array_map(fn($item) => is_array($item) ? $item[$key] ?? null : ($item->$key ?? null), $array);
    }

    /**
     * Filters an array using a callback, preserving keys.
     *
     * @param array $array The array to filter.
     * @param callable $callback The callback function to use. It receives both value and key.
     * @return array The filtered array.
     */
    public static function where(array $array, callable $callback): array
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }
}
