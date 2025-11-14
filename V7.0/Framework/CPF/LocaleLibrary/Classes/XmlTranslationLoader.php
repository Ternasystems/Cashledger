<?php

namespace TS_Locale\Classes;

use TS_Configuration\Classes\AbstractCls;
use TS_Locale\Interfaces\ITranslationLoader;

class XmlTranslationLoader extends AbstractCls implements ITranslationLoader
{
    private readonly string $basePath;

    /**
     * @param string $basePath The absolute path to the directory containing the .xml translation files.
     */
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    }

    /**
     * Loads all translation key-value pairs for a specific locale and domain.
     *
     * @param string $locale The locale identifier (e.g., 'en-US').
     * @param string $domain The translation domain (e.g., 'HomeLocale').
     * @return array<string, string> An associative array of translation keys and values.
     */
    public function load(string $locale, string $domain): array
    {
        $filepath = $this->basePath . DIRECTORY_SEPARATOR . $domain . '.xml';

        if (!file_exists($filepath) || !is_readable($filepath)) {
            return []; // File not found, return empty
        }

        // Suppress errors for invalid XML, we'll handle it
        libxml_use_internal_errors(true);
        $xml = simplexml_load_file($filepath);

        if ($xml === false) {
            // Log error or handle it
            libxml_clear_errors();
            return [];
        }

        // Use XPath to find all <item> elements under the specified locale
        // e.g., /locale/en-US/item
        $nodes = $xml->xpath("//{$locale}/item");

        if (empty($nodes)) {
            return []; // No items found for this locale
        }

        $translations = [];
        foreach ($nodes as $node) {
            // Get the 'name' attribute
            $key = (string)$node['name'];
            // Get the value of the <item> tag
            $value = (string)$node;

            if ($key) {
                $translations[$key] = $value;
            }
        }

        return $translations;
    }
}