<?php

declare(strict_types=1);

namespace TS_Locale\Classes;

use InvalidArgumentException;
use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\LocaleException;
use TS_Locale\Interfaces\ITranslationLoader;

/**
 * Loads translations from a standardized, nested JSON file format.
 * Assumes a single file contains multiple domains and locales.
 * File path is expected as: /path/to/locales/filename.json
 */
class JsonFileLoader extends AbstractCls implements ITranslationLoader
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
     * The domain now corresponds directly to the filename.
     *
     * @param string $locale The locale to load (e.g., 'en-US').
     * @param string $domain The domain, which is the filename without extension (e.g., 'HomeLocale').
     * @return array<string, string>
     * @throws LocaleException
     */
    public function load(string $locale, string $domain): array
    {
        $filePath = "{$this->basePath}/{$domain}.json";

        if (!file_exists($filePath)) {
            return []; // File for this domain doesn't exist.
        }

        $jsonContent = file_get_contents($filePath);
        if ($jsonContent === false) {
            throw new LocaleException('file_read_failed', [':path' => $filePath]);
        }

        $allTranslations = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new LocaleException('json_parse_failed', [
                ':path' => $filePath,
                ':reason' => json_last_error_msg()
            ]);
        }

        // The top-level key in the JSON file is the locale.
        return $allTranslations[$locale] ?? [];
    }
}
