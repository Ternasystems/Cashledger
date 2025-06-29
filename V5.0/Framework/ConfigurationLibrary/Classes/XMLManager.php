<?php

declare(strict_types=1);

namespace TS_Configuration\Classes;

use DOMDocument;
use DOMElement;
use DOMException;
use DOMNodeList;
use DOMXPath;
use TS_Configuration\Interfaces\IConfigurationManager;
use TS_Exception\Classes\XMLException;

/**
 * Manages configuration data stored in XML files.
 * Translates generic dot-notation paths to XPath for internal querying.
 */
class XMLManager extends AbstractCls implements IConfigurationManager
{
    /** The file path to the XML document. */
    protected readonly string $file;

    /** The main DOMDocument object. */
    protected DOMDocument $dom;

    /** The DOMXPath object for running queries. */
    protected DOMXPath $xpath;

    /** The root element of the XML document. */
    protected DOMElement $rootNode;

    public function __construct(string $filepath, ?string $rootElement = 'root')
    {
        if (file_exists($filepath) && strtolower(pathinfo($filepath, PATHINFO_EXTENSION)) !== 'xml') {
            throw new XMLException(['en' => "File provided is not an XML file. Path: $filepath"]);
        }

        $this->file = $filepath;
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;
        $this->dom->preserveWhiteSpace = false;

        if (!file_exists($this->file)) {
            if ($rootElement === null) {
                throw new XMLException(['en' => 'File does not exist and no root element name was provided.']);
            }
            $this->rootNode = $this->dom->createElement($rootElement);
            $this->dom->appendChild($this->rootNode);
            if (!$this->save()) {
                throw new XMLException(['en' => "Could not create new XML file at: $this->file"]);
            }
        } else {
            libxml_use_internal_errors(true);
            if (!$this->dom->load($this->file)) {
                $error = libxml_get_last_error();
                libxml_clear_errors();
                throw new XMLException(['en' => "Failed to load XML. Error: " . ($error ? trim($error->message) : 'Unknown')]);
            }
            $root = $this->dom->documentElement;
            if (!$root) {
                throw new XMLException(['en' => "XML file is empty or missing a root element."]);
            }
            $this->rootNode = $root;
        }

        $this->xpath = new DOMXPath($this->dom);
    }

    public function getFilename(): string
    {
        return $this->file;
    }

    public function get(string $path, mixed $default = null): mixed
    {
        $xpathQuery = $this->pathToXPath($path);
        $nodes = $this->query($xpathQuery);

        if ($nodes === false || $nodes->length === 0) {
            return $default;
        }

        if (str_contains($path, '@')) {
            return $nodes->item(0)?->nodeValue ?? $default;
        }

        if ($nodes->length > 1) {
            $values = [];
            foreach ($nodes as $node) {
                $values[] = $node->nodeValue;
            }
            return $values;
        }

        return $nodes->item(0)?->nodeValue ?? $default;
    }

    public function set(string $path, mixed $value): bool
    {
        $segments = explode('.', $path);
        $currentNode = $this->rootNode;

        foreach ($segments as $segment) {
            $nodeList = $this->xpath->query($segment, $currentNode);
            if ($nodeList->length === 0) {
                $childNode = $this->dom->createElement($segment);
                $currentNode->appendChild($childNode);
                $currentNode = $childNode;
            } else {
                $currentNode = $nodeList->item(0);
            }
        }

        $currentNode->nodeValue = (string)$value;
        return true;
    }

    public function delete(string $path): bool
    {
        $xpathQuery = $this->pathToXPath($path);
        $nodes = $this->query($xpathQuery);

        if ($nodes && $nodes->length > 0) {
            foreach ($nodes as $node) {
                $node->parentNode?->removeChild($node);
            }
            return true;
        }
        return false;
    }

    /**
     * Executes a raw XPath query on the document.
     *
     * @param string $xpathQuery The XPath query to execute.
     * @return DOMNodeList|false
     */
    public function query(string $xpathQuery): DOMNodeList|false
    {
        return $this->xpath->query($xpathQuery);
    }

    public function getDOM(): DOMDocument
    {
        return $this->dom;
    }

    public function getRootNode(): DOMElement
    {
        return $this->rootNode;
    }

    public function save(): bool
    {
        return $this->dom->save($this->file) !== false;
    }

    private function pathToXPath(string $path): string
    {
        $path = str_replace('@', '/@', $path);
        return './' . str_replace('.', '/', $path);
    }
}
