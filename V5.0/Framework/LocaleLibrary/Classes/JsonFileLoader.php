<?php

declare(strict_types=1);

namespace TS_Locale\Classes;

use InvalidArgumentException;
use TS_Exception\Classes\LocaleException;
use TS_Locale\Interfaces\TranslationLoaderInterface;

/**
 * Loads translations from a standardized, nested JSON file format.
 * Assumes a single file contains multiple domains and locales.
 * File path is expected as: /path/to/locales/filename.json
 */
class JsonFileLoader implements TranslationLoaderInterface
{
    private string $basePath;

    public function __construct(string $basePath)
    {
        if (!is_dir($basePath)) {
            throw new InvalidArgumentException("The specified path for locale files is not a valid directory: $basePath");
        }
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    }

    /**
     * Loads translations for a specific locale and domain.
     * The domain is now a nested key within the JSON file.
     *
     * @param string $locale The locale to load (e.g., 'en-US').
     * @param string $domain The domain key (e.g., 'HomeLocale.Presentation.Home').
     * @return array<string, string>
     * @throws LocaleException
     */
    public function load(string $locale, string $domain): array
    {
        // The first part of the domain is the filename.
        [$fileName] = explode('.', $domain, 2);
        $filePath = "{$this->basePath}/{$fileName}.json";

        if (!file_exists($filePath)) {
            return []; // File for this domain doesn't exist.
        }

        $jsonContent = file_get_contents($filePath);
        if ($jsonContent === false) {
            throw new LocaleException(['en' => "Could not read translation file: $filePath"]);
        }

        $allTranslations = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new LocaleException(['en' => "Error decoding JSON from file: $filePath. Reason: " . json_last_error_msg()]);
        }

        // Navigate the nested structure to find the translations.
        // e.g., from $domain = 'HomeLocale.Presentation.Home'
        // we look for $allTranslations['en-US']['Presentation.Home']
        $domainKey = substr(strstr($domain, '.'), 1); // Extracts 'Presentation.Home'

        return $allTranslations[$locale][$domainKey] ?? [];
    }
}
