<?php

declare(strict_types=1);

namespace TS_Utility\Helpers;

use NumberFormatter;
use TS_Configuration\Classes\AbstractCls;

/**
 * A collection of static helper methods for numeric formatting.
 * NOTE: Requires the 'php-intl' PHP extension.
 */
final class NumberHelper extends AbstractCls
{
    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Formats a number with locale-specific thousand/decimal separators.
     *
     * @param float|int $number The number to format.
     * @param string $locale The locale (e.g., 'en_US', 'de_DE').
     * @return string The formatted number string.
     */
    public static function format(float|int $number, string $locale = 'en_US'): string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        return $formatter->format($number) ?: '';
    }

    /**
     * Formats a number as a currency string with the correct symbol and formatting.
     *
     * @param float|int $amount The currency value.
     * @param string $currencyCode The 3-letter ISO currency code (e.g., 'USD', 'EUR', 'XAF').
     * @param string $locale The locale that determines formatting (e.g., 'en_US', 'fr_CM').
     * @return string The formatted currency string.
     */
    public static function formatCurrency(float|int $amount, string $currencyCode = 'USD', string $locale = 'en_US'): string
    {
        $formatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        return $formatter->formatCurrency($amount, $currencyCode) ?: '';
    }
}