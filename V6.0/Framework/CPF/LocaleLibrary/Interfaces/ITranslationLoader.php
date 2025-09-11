<?php

declare(strict_types=1);

namespace TS_Locale\Interfaces;

/**
 * Defines the contract for a class that can load translation data for a given locale.
 */
interface ITranslationLoader
{
    /**
     * Loads all translation key-value pairs for a specific locale and domain.
     * The domain typically corresponds to a file name (e.g., "HomeLocale").
     *
     * @param string $locale The locale identifier (e.g., 'en-US').
     * @param string $domain The translation domain.
     * @return array<string, string> An associative array of translation keys and values.
     */
    public function load(string $locale, string $domain): array;
}
