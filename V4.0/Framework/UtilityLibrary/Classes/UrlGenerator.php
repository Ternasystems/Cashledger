<?php

namespace TS_Utility\Classes;

use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\UtilsException;

class UrlGenerator extends AbstractCls
{
    private array $config;
    private string $pattern;

    public function __construct($configFilePath) {
        $this->config = json_decode(file_get_contents($configFilePath), true);
        $this->pattern = $this->config['pattern'];
    }

    /* Inherited protected methods */

    // Method to set the exception property
    protected function setException(): void
    {
        $this->exception = new UtilsException();
    }

    // Method to get the exception property
    public function getException(): void
    {
        throw $this->exception;
    }

    // Extract the language from the current URL
    public function language(string $url): ?string {
        // Use regular expression to capture language segment
        if (preg_match('/\/(\w{2}-\w{2})\//', $url, $matches)) {
            return $matches[1]; // Return the language code (e.g., 'en', 'fr')
        }

        return null; // Return null if language is not found
    }

    public function application(string $url): ?string
    {
        // Define the pattern for the URL (e.g., "/{language}/{application}/{controller}/{action}")
        // Here, we're focusing on extracting the {application} part
        if (preg_match('/\/(\w{2}-\w{2})\/(\w+)\//', $url, $matches)) {
            // The second element in $matches array corresponds to the application name
            return $matches[2]; // Return the application name
        }

        return null; // Return null if the URL doesn't match the expected pattern
    }

    public function controller(string $url): ?string
    {
        if (preg_match('/\/(\w{2}-\w{2})\/(\w+)\/(\w+)\//', $url, $matches)) {
            return $matches[3];
        }

        return null;
    }

    public function action(string $url): ?string
    {
        if (preg_match('/\/(\w{2}-\w{2})\/(\w+)\/(\w+)\/(\w+)/', $url, $matches)) {
            return $matches[4];
        }

        return null;
    }

    // Generate the URL dynamically based on the provided params
    public function generate(array $params = []) {
        // Use the default language if not provided
        $params['language'] = $params['language'] ?? $this->config['default']['language']; // default language
        $params['application'] = $params['application'] ?? $this->config['default']['application']; // default application
        $params['controller'] = $params['controller'] ?? $this->config['default']['controller']; // default controller
        $params['action'] = $params['action'] ?? $this->config['default']['action']; // default action

        // Replace placeholders in the pattern with actual values
        $url = $this->pattern;

        foreach ($params as $key => $value) {
            $url = str_replace('{' . $key . '}', $value, $url);
        }

        $baseUri = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        return "$baseUri$url";
    }
}