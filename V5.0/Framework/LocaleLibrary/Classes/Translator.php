<?php

declare(strict_types=1);

namespace TS_Locale\Classes;

use TS_Locale\Interfaces\TranslationLoaderInterface;

/**
 * The main service for handling translations from multiple sources.
 */
final class Translator
{
    /** @var TranslationLoaderInterface[] */
    private array $loaders = [];
    private string $currentLocale;
    private string $fallbackLocale;

    /** @var array<string, array<string, string>> In-memory cache for loaded translations. */
    private array $translations = [];

    public function __construct(
        string $currentLocale,
        string $fallbackLocale = 'en-US'
    ) {
        $this->currentLocale = $currentLocale;
        $this->fallbackLocale = $fallbackLocale;
    }

    /**
     * Adds a translation loader to the translator.
     * Loaders will be checked in the order they are added.
     */
    public function addLoader(string $domainPrefix, TranslationLoaderInterface $loader): void
    {
        $this->loaders[$domainPrefix] = $loader;
    }

    /**
     * Translates a given key, with optional placeholders.
     *
     * @param string $key The translation key (e.g., 'DBException.connection_failed').
     * @param array<string, string|int> $placeholders Key-value pairs for replacement.
     * @return string The translated string, or the key itself if not found.
     */
    public function trans(string $key, array $placeholders = []): string
    {
        [$domain, $translationKey] = explode('.', $key, 2);

        $message = $this->findTranslation($this->currentLocale, $domain, $translationKey)
            ?? $this->findTranslation($this->fallbackLocale, $domain, $translationKey)
            ?? $key; // Return the key as a last resort.

        // Replace placeholders
        if (!empty($placeholders) && str_contains($message, ':')) {
            foreach ($placeholders as $placeholder => $value) {
                $message = str_replace($placeholder, (string)$value, $message);
            }
        }

        return $message;
    }

    /**
     * Finds a translation by iterating through registered loaders.
     */
    private function findTranslation(string $locale, string $domain, string $key): ?string
    {
        // First, check the cache
        if (isset($this->translations[$locale][$domain][$key])) {
            return $this->translations[$locale][$domain][$key];
        }

        // If not cached, try to load it from the registered loaders
        foreach ($this->loaders as $prefix => $loader) {
            // Check if the domain starts with the loader's prefix
            if (str_starts_with($domain, $prefix)) {
                // Load and cache the entire domain file
                $this->translations[$locale][$domain] = $loader->load($locale, $domain);
                // Now check again for the specific key
                if (isset($this->translations[$locale][$domain][$key])) {
                    return $this->translations[$locale][$domain][$key];
                }
            }
        }

        return null;
    }
}
