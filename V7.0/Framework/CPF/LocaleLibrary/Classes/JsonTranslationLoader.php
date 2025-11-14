<?php

namespace TS_Locale\Classes;

use TS_Configuration\Classes\AbstractCls;
use TS_Locale\Interfaces\ITranslationLoader;

/**
 * A translation loader that reads strings from JSON files.
 *
 * It expects files to be named after the domain (e.g., "DIException.json")
 * and for the JSON to be structured with the locale as the top-level key.
 * {
 * "en-US": { "key": "value" },
 * "fr-FR": { "key": "valeur" }
 * }
 */
class JsonTranslationLoader extends AbstractCls implements ITranslationLoader
{
    private readonly string $basePath;

    /**
     * @param string $basePath The absolute path to the directory containing the .json translation files.
     */
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    }

    /**
     * Loads all translation key-value pairs for a specific locale and domain.
     *
     * @param string $locale The locale identifier (e.g., 'en-US').
     * @param string $domain The translation domain (e.g., 'DIException').
     * @return array<string, string> An associative array of translation keys and values.
     */
    public function load(string $locale, string $domain): array
    {
        $filepath = $this->basePath . DIRECTORY_SEPARATOR . $domain . '.json';

        if (!file_exists($filepath) || !is_readable($filepath)) {
            return []; // File not found, return empty
        }

        $content = file_get_contents($filepath);
        if ($content === false) {
            return []; // Failed to read file
        }

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Log error or handle it, but return empty for now
            return [];
        }

        // Return the array for the specific locale, or an empty array
        return $data[$locale] ?? [];
    }
}