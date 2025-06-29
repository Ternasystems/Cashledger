<?php

declare(strict_types=1);

namespace TS_Utility\Helpers;

/**
 * A collection of static helper methods for string manipulation.
 * This class cannot be instantiated.
 */
final class StringHelper
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
}