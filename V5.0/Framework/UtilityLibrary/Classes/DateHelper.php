<?php

declare(strict_types=1);

namespace TS_Utility\Helpers;

use DateTime;
use DateTimeInterface;
use IntlDateFormatter;
use NumberFormatter;

/**
 * A collection of static helper methods for date/time formatting.
 * NOTE: Requires the 'php-intl' PHP extension.
 */
final class DateHelper
{
    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Formats a date to a localized string using IntlDateFormatter.
     *
     * @param DateTimeInterface $date The date to format.
     * @param string $locale The locale (e.g., 'en_US', 'fr_FR').
     * @param int $dateStyle IntlDateFormatter constant (FULL, LONG, MEDIUM, SHORT).
     * @param int $timeStyle IntlDateFormatter constant (FULL, LONG, MEDIUM, SHORT, NONE).
     * @return string The formatted date string.
     */
    public static function format(DateTimeInterface $date, string $locale = 'en_US', int $dateStyle = IntlDateFormatter::MEDIUM, int $timeStyle = IntlDateFormatter::SHORT): string
    {
        $formatter = new IntlDateFormatter($locale, $dateStyle, $timeStyle);
        return $formatter->format($date) ?: '';
    }

    /**
     * Formats a date to a human-readable relative time string (e.g., "2 hours ago").
     *
     * @param DateTimeInterface $date The date to compare to now.
     * @param string $locale The locale (e.g., 'en_US', 'fr_FR').
     * @return string The relative time string.
     */
    public static function timeAgo(DateTimeInterface $date, string $locale = 'en_US'): string
    {
        $now = new DateTime();
        $diff = $now->diff($date);

        // --- Fallback implementation for environments without the intl extension ---
        if ($diff->y > 0) return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
        if ($diff->m > 0) return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
        if ($diff->d > 0) return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
        if ($diff->h > 0) return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
        if ($diff->i > 0) return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
        if ($diff->s > 0) return $diff->s . ' second' . ($diff->s > 1 ? 's' : '') . ' ago';

        return 'just now';
    }
}