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
    protected DOMDocument $dom {
        get {
            return $this->dom;
        }
    }

    /** The DOMXPath object for running queries. */
    protected DOMXPath $xpath;

    /** The root element of the XML document. */
    protected DOMElement $rootNode {
        get {
            return $this->rootNode;
        }
    }

    /**
     * Constructs the XMLManager, loading an existing XML file or creating a new one.
     *
     * @param string $filepath The path to the XML file.
     * @param string|null $rootElement The name of the root element for a new file.
     * @throws XMLException|DOMException if the file is invalid or cannot be created/read.
     */
    public function __construct(string $filepath, ?string $rootElement = 'root')
    {
        if (file_exists($filepath) && strtolower(pathinfo($filepath, PATHINFO_EXTENSION)) !== 'xml') {
            throw new XMLException('file_not_xml', [':path' => $filepath]);
        }

        $this->file = $filepath;
        $this->load($rootElement);
    }

    /**
     * Internal method to initialize the DOM structure.
     */
    private function load(?string $rootElement): void
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;
        $this->dom->preserveWhiteSpace = false;

        if (!file_exists($this->file)) {
            if ($rootElement === null) {
                // Should not happen during unserialize if cache is valid
                throw new XMLException('no_root_element');
            }
            $this->rootNode = $this->dom->createElement($rootElement);
            $this->dom->appendChild($this->rootNode);
            if (!$this->save()) {
                throw new XMLException('file_creation_failed', [':path' => $this->file]);
            }
        } else {
            libxml_use_internal_errors(true);
            if (!$this->dom->load($this->file)) {
                $error = libxml_get_last_error();
                libxml_clear_errors();
                throw new XMLException('load_failed', [':reason' => ($error ? trim($error->message) : 'Unknown')]);
            }
            $root = $this->dom->documentElement;
            if (!$root) {
                throw new XMLException('empty_or_no_root');
            }
            $this->rootNode = $root;
        }

        $this->xpath = new DOMXPath($this->dom);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilename(): string
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     * This enhanced implementation can create nested nodes, set attributes,
     * and build complex structures from an array value.
     * @throws DOMException
     */
    public function set(string $path, mixed $value): bool
    {
        $segments = explode('.', $path);
        $lastSegment = array_pop($segments);
        $parentNodePath = implode('.', $segments);

        $parentNode = $this->rootNode;
        if (!empty($parentNodePath)) {
            $parentXPath = $this->pathToXPath($parentNodePath);
            $parentNodes = $this->query($parentXPath);

            if ($parentNodes !== false && $parentNodes->length > 0) {
                $parentNode = $parentNodes->item(0);
            } else {
                // If parent path does not exist, create it.
                $current = $this->rootNode;
                foreach ($segments as $segment) {
                    $nodeList = $this->xpath->query($segment, $current);
                    if ($nodeList->length === 0) {
                        $child = $this->dom->createElement($segment);
                        $current->appendChild($child);
                        $current = $child;
                    } else {
                        $current = $nodeList->item(0);
                    }
                }
                $parentNode = $current;
            }
        }

        // Handle attribute setting
        if (str_starts_with($lastSegment, '@')) {
            $attributeName = substr($lastSegment, 1);
            if ($parentNode instanceof DOMElement) {
                $parentNode->setAttribute($attributeName, (string)$value);
                return true;
            }
            return false;
        }

        // Remove existing nodes with the same name before creating new ones
        $existingNodes = $this->xpath->query($lastSegment, $parentNode);
        foreach ($existingNodes as $node) {
            $node->parentNode?->removeChild($node);
        }

        // Create new node(s)
        if (is_array($value)) {
            $this->createNodesRecursive($parentNode, $lastSegment, $value);
        } else {
            $newNode = $this->dom->createElement($lastSegment, (string)$value);
            $parentNode->appendChild($newNode);
        }

        return true;
    }

    /**
     * Recursively creates nodes from an array structure.
     * @throws DOMException
     */
    private function createNodesRecursive(DOMElement $parent, string $nodeName, array $data): void
    {
        // Check if it's a list (numeric keys) or an associative array
        if (array_keys($data) === range(0, count($data) - 1)) {
            foreach ($data as $item) {
                if (is_array($item)) {
                    $childNode = $this->dom->createElement($nodeName);
                    $this->createNodesRecursive($childNode, '', $item); // Pass empty nodeName
                    $parent->appendChild($childNode);
                }
            }
        } else { // Associative array
            if ($nodeName !== '') {
                $newNode = $this->dom->createElement($nodeName);
                foreach ($data as $key => $val) {
                    if (str_starts_with($key, '@')) {
                        $newNode->setAttribute(substr($key, 1), (string)$val);
                    } elseif (is_array($val)) {
                        $this->createNodesRecursive($newNode, $key, $val);
                    } else {
                        $child = $this->dom->createElement($key, (string)$val);
                        $newNode->appendChild($child);
                    }
                }
                $parent->appendChild($newNode);
            } else { // Append to parent directly
                foreach ($data as $key => $val) {
                    if (str_starts_with($key, '@')) {
                        $parent->setAttribute(substr($key, 1), (string)$val);
                    }
                }
            }
        }
    }


    /**
     * {@inheritdoc}
     */
    public function delete(string $path): bool
    {
        $xpathQuery = $this->pathToXPath($path);
        $nodes = $this->query($xpathQuery);

        if ($nodes && $nodes->length > 0) {
            foreach ($nodes as $node) {
                // If it's an attribute, remove it from the element
                if ($node->nodeType === XML_ATTRIBUTE_NODE) {
                    $node->ownerElement->removeAttribute($node->nodeName);
                } else {
                    $node->parentNode?->removeChild($node);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Executes a raw XPath query on the document.
     */
    public function query(string $xpathQuery): DOMNodeList|false
    {
        return $this->xpath->query($xpathQuery);
    }

    /**
     * Persists all in-memory changes back to the XML file.
     */
    public function save(): bool
    {
        return $this->dom->save($this->file) !== false;
    }

    /**
     * Converts a dot-notation path to a simple XPath query.
     */
    private function pathToXPath(string $path): string
    {
        $path = str_replace('@', '/@', $path);
        return './' . str_replace('.', '/', $path);
    }

    /**
     * Serialization hook: Only save the file path, do NOT save the DOMDocument/DOMNode.
     */
    public function __serialize(): array
    {
        return [
            'file' => $this->file
        ];
    }

    /**
     * Unserialization hook: Restore the file path and reload the DOM.
     */
    public function __unserialize(array $data): void
    {
        $this->file = $data['file'];
        // When restoring from cache, the file should already exist, so rootElement defaults to null.
        $this->load(null);
    }
}

