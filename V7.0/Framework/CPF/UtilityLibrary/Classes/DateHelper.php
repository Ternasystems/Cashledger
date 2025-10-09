<?php

declare(strict_types=1);

namespace TS_Utility\Helpers;

use DateTime;
use DateTimeInterface;
use IntlDateFormatter;
use TS_Configuration\Classes\AbstractCls;

/**
 * A collection of static helper methods for date/time formatting.
 * NOTE: Requires the 'php-intl' PHP extension.
 */
final class DateHelper extends AbstractCls
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
        // Check if the intl extension is loaded
        if (!class_exists('IntlDateFormatter'))
            return $date->format('Y-m-d H:i:s'); // Fallback to a standard format

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
        $interval = $now->diff($date);

        if ($interval->y > 0) return $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
        if ($interval->m > 0) return $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
        if ($interval->d > 0) return $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
        if ($interval->h > 0) return $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
        if ($interval->i > 0) return $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
        if ($interval->s > 0) return $interval->s . ' second' . ($interval->s > 1 ? 's' : '') . ' ago';

        return 'just now';
    }
}