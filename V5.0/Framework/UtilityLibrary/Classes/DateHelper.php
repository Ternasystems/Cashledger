<?php

declare(strict_types=1);

namespace TS_Utility\Helpers;

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
     * @param \DateTimeInterface $date The date to format.
     * @param string $locale The locale (e.g., 'en_US', 'fr_FR').
     * @param int $dateStyle IntlDateFormatter constant (FULL, LONG, MEDIUM, SHORT).
     * @param int $timeStyle IntlDateFormatter constant (FULL, LONG, MEDIUM, SHORT, NONE).
     * @return string The formatted date string.
     */
    public static function format(\DateTimeInterface $date, string $locale = 'en_US', int $dateStyle = \IntlDateFormatter::MEDIUM, int $timeStyle = \IntlDateFormatter::SHORT): string
    {
        $formatter = new \IntlDateFormatter($locale, $dateStyle, $timeStyle);
        return $formatter->format($date) ?: '';
    }

    /**
     * Formats a date to a human-readable relative time string (e.g., "2 hours ago").
     *
     * @param \DateTimeInterface $date The date to compare to now.
     * @param string $locale The locale (e.g., 'en_US', 'fr_FR').
     * @return string The relative time string.
     */
    public static function timeAgo(\DateTimeInterface $date, string $locale = 'en_US'): string
    {
        $formatter = new \IntlRelativeTimeFormatter($locale, \NumberFormatter::FORMAT_WIDTH_DEFAULT);
        $diff = (new \DateTime())->getTimestamp() - $date->getTimestamp();

        if ($diff < 60) return $formatter->format(-$diff, 'second');

        $minutes = round($diff / 60);
        if ($minutes < 60) return $formatter->format(-$minutes, 'minute');

        $hours = round($diff / 3600);
        if ($hours < 24) return $formatter->format(-$hours, 'hour');

        $days = round($diff / 86400);
        return $formatter->format(-$days, 'day');
    }
}