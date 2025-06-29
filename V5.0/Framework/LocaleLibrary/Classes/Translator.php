<?php

declare(strict_types=1);

namespace TS_Locale\Classes;

use TS_Locale\Interfaces\TranslationLoaderInterface;

/**
 * The main service for handling translations.
 */
final class Translator
{
    private TranslationLoaderInterface $loader;
    private string $currentLocale;
    private string $fallbackLocale;

    /** @var array<string, array<string, string>> In-memory cache for loaded translations. */
    private array $translations = [];

    public function __construct(
        TranslationLoaderInterface $loader,
        string $currentLocale,
        string $fallbackLocale = 'en-US'
    ) {
        $this->loader = $loader;
        $this->currentLocale = $currentLocale;
        $this->fallbackLocale = $fallbackLocale;
    }

    /**
     * Translates a given key, with optional placeholders.
     *
     * @param string $key The translation key (e.g., 'home.title').
     * @param array<string, string|int> $placeholders Key-value pairs for replacement.
     * @return string The translated string, or the key itself if not found.
     */
    public function trans(string $key, array $placeholders = []): string
    {
        [$domain, $translationKey] = explode('.', $key, 2);

        // Load translations for the domain if not already cached.
        $this->loadDomain($this->currentLocale, $domain);
        $this->loadDomain($this->fallbackLocale, $domain);

        $message = $this->translations[$this->currentLocale][$domain][$translationKey]
            ?? $this->translations[$this->fallbackLocale][$domain][$translationKey]
            ?? $key; // Return the key as a last resort.

        // Replace placeholders like :name with their values.
        if (!empty($placeholders)) {
            foreach ($placeholders as $placeholder => $value) {
                $message = str_replace(':' . $placeholder, (string)$value, $message);
            }
        }

        return $message;
    }

    /**
     * Loads the translations for a given domain into the cache.
     */
    private function loadDomain(string $locale, string $domain): void
    {
        if (isset($this->translations[$locale][$domain])) {
            return; // Already loaded.
        }

        $this->translations[$locale][$domain] = $this->loader->load($locale, $domain);
    }
}
