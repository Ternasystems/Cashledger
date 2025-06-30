<?php

declare(strict_types=1);

namespace TS_Locale\Classes;

use DOMElement;
use TS_Configuration\Classes\XMLManager;
use TS_Exception\Classes\LocaleException;
use TS_Exception\Classes\XMLException;
use TS_Locale\Interfaces\TranslationLoaderInterface;

/**
 * Loads translations from a standardized XML file format using the framework's XMLManager.
 * Assumes a single file per domain contains all locales.
 * File path is expected as: /path/to/locales/{domain}.xml
 */
class XmlFileLoader implements TranslationLoaderInterface
{
    private string $basePath;

    /**
     * @throws LocaleException
     */
    public function __construct(string $basePath)
    {
        if (!is_dir($basePath)) {
            throw new LocaleException('loader_path_invalid', [':path' => $basePath]);
        }
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    }

    /**
     * Loads translations for a specific locale from a domain file.
     *
     * @param string $locale The locale to load (e.g., 'en-US').
     * @param string $domain The domain file name (e.g., 'HomeLocale').
     * @return array<string, string>
     * @throws LocaleException
     */
    public function load(string $locale, string $domain): array
    {
        $filePath = "{$this->basePath}/{$domain}.xml";

        if (!file_exists($filePath)) {
            return []; // File for this domain doesn't exist, return empty array.
        }

        try {
            // Use our robust XMLManager for consistency.
            $xmlManager = new XMLManager($filePath);

            // Use a specific XPath to find the correct locale section in the file.
            $xpathQuery = "/locale/{$locale}/item";
            $nodes = $xmlManager->query($xpathQuery);

            if ($nodes === false || $nodes->length === 0) {
                return []; // No translation items found for this specific locale.
            }

            $translations = [];
            foreach ($nodes as $node) {
                // Ensure the node is an element and has the 'name' attribute.
                if ($node instanceof DOMElement && $node->hasAttribute('name')) {
                    $key = $node->getAttribute('name');
                    $value = $node->nodeValue;
                    $translations[$key] = $value;
                }
            }
            return $translations;

        } catch (XMLException $e) {
            // Wrap the XML exception in a LocaleException to provide clear context.
            throw new LocaleException(
                'xml_parse_failed',
                [':path' => $filePath, ':reason' => $e->getTranslatedMessage()],
                (int)$e->getCode(),
                $e
            );
        }
    }
}
