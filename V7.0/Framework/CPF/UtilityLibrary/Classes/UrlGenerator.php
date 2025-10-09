<?php

declare(strict_types=1);

namespace TS_Utility\Classes;

use InvalidArgumentException;
use TS_Configuration\Classes\AbstractCls;

/**
 * Generates and parses application-specific URLs based on a defined pattern.
 */
final class UrlGenerator extends AbstractCls
{
    private string $pattern;
    private array $defaults;
    private string $baseUri;

    /**
     * @param array $config The URL configuration, typically including a 'pattern' and 'default' values.
     */
    public function __construct(array $config)
    {
        if (empty($config['pattern']) || empty($config['default'])) {
            throw new InvalidArgumentException('URL generator configuration is missing required keys: "pattern" and "default".');
        }

        $this->pattern = $config['pattern'];
        $this->defaults = $config['default'];

        // The base URI can also be part of the config for more flexibility
        $this->baseUri = $config['base_uri'] ?? rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    }

    /**
     * Extracts the language segment from a URL path.
     * Example: from '/en-US/Presentation/Home/Index', it returns 'en-US'.
     */
    public function language(string $urlPath): ?string
    {
        if (preg_match('#/(\w{2}-\w{2})/#', $urlPath, $matches))
            return $matches[1];
        return null;
    }

    /**
     * Extracts the application segment from a URL path.
     * Example: from '/en-US/Presentation/Home/Index', it returns 'Presentation'.
     */
    public function application(string $urlPath): ?string
    {
        if (preg_match('#/(\w{2}-\w{2})/(\w+)/#', $urlPath, $matches))
            return $matches[2];
        return null;
    }

    /**
     * Extracts the controller segment from a URL path.
     * Example: from '/en-US/Presentation/Home/Index', it returns 'Home'
     */
    public function controller(string $urlPath): ?string
    {
        if (preg_match('#/(\w{2}-\w{2})/(\w+)/(\w+)/#', $urlPath, $matches))
            return $matches[3];
        return null;
    }

    /**
     * Extracts the action segment from a URL path.
     * Example: from '/en-US/Presentation/Home/Index', it returns 'Index'
     */
    public function action(string $urlPath): ?string
    {
        if (preg_match('#/(\w{2}-\w{2})/(\w+)/(\w+)/(\w+)/?#', $urlPath, $matches))
            return $matches[4];
        return null;
    }

    /**
     * Generates a full URL based on a routing pattern and provided parameters.
     *
     * @param array<string, string> $params Associative array of URL parts (e.g., ['application' => 'Inventory', 'controller' => 'Products']).
     * @return string The fully-formed, application-aware URL.
     */
    public function generate(array $params = []): string
    {
        // Merge provided params with defaults
        $params = array_merge($this->defaults, $params);

        // Replace placeholders in the pattern with actual values
        $url = $this->pattern;
        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', (string) $value, $url);
        }

        return $this->baseUri . $url;
    }
}