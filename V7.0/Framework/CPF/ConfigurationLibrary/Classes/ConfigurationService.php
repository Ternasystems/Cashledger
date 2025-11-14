<?php

namespace TS_Configuration\Classes;

use TS_Database\Classes\DBCredentials;
use TS_Database\Enums\DBDriver;

/**
 * A Singleton service to provide easy access to config.xml values.
 *
 * It replaces the old StaticData.php and Utils.php pattern.
 * It loads the XMLManager once and caches the values for fast access.
 */
class ConfigurationService extends AbstractCls
{
    private readonly XMLManager $xml;
    private array $cache = []; // Internal cache

    public function __construct(XMLManager $xmlManager)
    {
        $this->xml = $xmlManager;
    }

    /**
     * A helper to get a value from cache or load it from XML.
     */
    private function get(string $key, callable $loader): mixed
    {
        if (!isset($this->cache[$key])) {
            $this->cache[$key] = $loader($this->xml);
        }
        return $this->cache[$key];
    }

    /**
     * Gets the database credentials from config.xml.
     */
    public function getDbCredentials(): ?DBCredentials
    {
        return $this->get('db_creds', function (XMLManager $xml) {
            $driverStr = $xml->get('dbConnection@PDODriver', 'pgsql');
            $dsn = $xml->get('dbConnection@DSN');
            $user = $xml->get('dbConnection@userName');
            $pass = $xml->get('dbConnection@passWord');

            // Parse DSN to get host and dbname
            // This is a simplified parser; a real one would be more robust.
            $host = 'localhost';
            $dbname = 'cashledger';
            if (preg_match('/host=([\w\.]+)/', $dsn, $matches)) {
                $host = $matches[1];
            }
            if (preg_match('/dbname=([\w\.]+)/', $dsn, $matches)) {
                $dbname = $matches[1];
            }

            $driver = DBDriver::tryFrom(strtolower($driverStr));

            if (!$driver || !$user || !$pass || !$host || !$dbname) {
                return null;
            }

            return new DBCredentials($driver, $host, $dbname, $user, $pass);
        });
    }

    /**
     * Gets a specific image path by name.
     * RENAMED from getImagePath
     */
    public function getImage(string $name): ?string
    {
        // We don't cache all images, just the one requested.
        return $this->get("image_{$name}", function (XMLManager $xml) use ($name) {
            $node = $xml->query("//images/image[@name='{$name}']");
            return $node[0]?->getAttribute('src');
        });
    }

    /**
     * ADDED: Gets all image paths as an associative array.
     * @return array<string, string>
     */
    public function getImages(): array
    {
        return $this->get('all_images', function (XMLManager $xml) {
            $images = [];
            $nodeList = $xml->query("//images/image");
            if (!$nodeList) {
                return [];
            }
            foreach ($nodeList as $node) {
                $name = $node->getAttribute('name');
                $src = $node->getAttribute('src');
                if ($name && $src) {
                    $images[$name] = $src;
                }
            }
            return $images;
        });
    }

    /**
     * ADDED: Gets a language name by its ISO code.
     */
    public function getLanguage(string $iso): ?string
    {
        return $this->get("lang_{$iso}", function (XMLManager $xml) use ($iso) {
            $node = $xml->query("//languages/lang[@iso='{$iso}']");
            return $node[0]?->getAttribute('name');
        });
    }

    /**
     * Gets the default language ISO code.
     */
    public function getDefaultLanguage(): string
    {
        return $this->get('default_lang', function (XMLManager $xml) {
            // This XPath finds the <lang> tag where @default is "true" and returns its @iso attribute.
            $node = $xml->query('//languages/lang[@default="true"]');
            return $node[0]?->getAttribute('iso') ?? 'en-US';
        });
    }

    /**
     * ADDED: Gets all languages as an associative array (iso => name).
     * @return array<string, string>
     */
    public function getLanguages(): array
    {
        return $this->get('all_languages', function (XMLManager $xml) {
            $languages = [];
            $nodeList = $xml->query("//languages/lang");
            if (!$nodeList) {
                return [];
            }
            foreach ($nodeList as $node) {
                $iso = $node->getAttribute('iso');
                $name = $node->getAttribute('name');
                if ($iso && $name) {
                    $languages[$iso] = $name;
                }
            }
            return $languages;
        });
    }

    /**
     * ADDED: Gets the default application details.
     * @return array<string, string>|null
     */
    public function getDefaultApp(): ?array
    {
        return $this->get('default_app', function (XMLManager $xml) {
            $nodeList = $xml->query('//applications/app[@default="true"]');
            if (!$nodeList || $nodeList->length === 0) {
                return null;
            }
            $node = $nodeList[0];
            return [
                'name' => $node->getAttribute('name'),
                'controller' => $node->getAttribute('controller'),
                'action' => $node->getAttribute('action')
            ];
        });
    }

    /**
     * ADDED: Gets a link path by its name (e.g., 'bootstrap', 'css').
     */
    public function getPath(string $name): ?string
    {
        return $this->get("path_{$name}", function (XMLManager $xml) use ($name) {
            $node = $xml->query("//links/link[@name='{$name}Path']");
            return $node[0]?->getAttribute('path');
        });
    }

    /**
     * ADDED: Gets all link paths as an associative array.
     * @return array<string, string>
     */
    public function getPaths(): array
    {
        return $this->get('all_paths', function (XMLManager $xml) {
            $paths = [];
            $nodeList = $xml->query("//links/link");
            if (!$nodeList) {
                return [];
            }
            foreach ($nodeList as $node) {
                $name = $node->getAttribute('name');
                $path = $node->getAttribute('path');
                // Clean the name from '...Path' to '...'
                if (str_ends_with($name, 'Path')) {
                    $name = substr($name, 0, -4);
                }
                if ($name && $path) {
                    $paths[$name] = $path;
                }
            }
            return $paths;
        });
    }

    /**
     * ADDED: Gets the server's IP address.
     */
    public function getIP(): string
    {
        return $this->get('ip_address', function () {
            return gethostbyname(gethostname()) ?? '127.0.0.1';
        });
    }

    /**
     * ADDED: Gets the company name from the config.
     */
    public function getCompanyName(): string
    {
        return $this->get('company_name', function (XMLManager $xml) {
            $node = $xml->query('//company');
            return $node[0]?->getAttribute('name') ?? '';
        });
    }
}