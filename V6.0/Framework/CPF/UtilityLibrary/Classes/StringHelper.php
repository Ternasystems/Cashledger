<?php

declare(strict_types=1);

namespace TS_Utility\Helpers;

use TS_Configuration\Classes\AbstractCls;

/**
 * A collection of static helper methods for string manipulation.
 * This class cannot be instantiated.
 */
final class StringHelper extends AbstractCls
{
    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Truncates a string to a specified length, appending an ellipsis.
     *
     * @param string $text The text to truncate.
     * @param int $maxLength The maximum length of the output string.
     * @param string $ellipsis The string to append if truncated.
     * @return string The truncated text.
     */
    public static function truncate(string $text, int $maxLength = 100, string $ellipsis = '...'): string
    {
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }
        $truncated = mb_substr($text, 0, $maxLength - mb_strlen($ellipsis));
        return $truncated . $ellipsis;
    }

    /**
     * Converts a string into a URL-friendly "slug".
     *
     * @param string $string The string to convert.
     * @param string $separator The separator to use between words.
     * @return string The generated slug.
     */
    public static function slug(string $string, string $separator = '-'): string
    {
        // Convert to lowercase and transliterate to ASCII
        $string = strtolower(trim($string));
        $string = preg_replace('~[^\pL\d]+~u', $separator, $string);
        $string = iconv('utf-8', 'us-ascii//TRANSLIT', $string);

        // Remove unwanted characters
        $string = preg_replace('~[^-\w]+~', '', $string);

        // Trim separators from the beginning and end
        $string = trim($string, $separator);

        // Remove duplicate separators
        $string = preg_replace('~-+~', $separator, $string);

        return $string ?: 'n-a';
    }

    /**
     * Generates a cryptographically secure random string.
     *
     * @param int $length The desired length of the random string.
     * @return string
     * @throws \Exception if a secure source of randomness cannot be found.
     */
    public static function random(int $length = 16): string
    {
        // Ensure the length is even for hex conversion
        $byteLength = (int) ceil($length / 2);
        $randomBytes = random_bytes($byteLength);
        return substr(bin2hex($randomBytes), 0, $length);
    }
}