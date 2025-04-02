<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the J�oline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Configuration\Interfaces;

header('Content-Type: text/html; charset=utf-8');

/*
 * iXMLWriter interface
 */
interface iXMLReader
{
    // Method to get the XML file name
    public function GetXMLFileName() : string;

    // Method to get the XML DOM Object
    public function GetXMLDOM() : \DOMDocument;

    // Method to get the XPath object
    public function GetXMLXPath() : \DOMXPath;

    // Method to get the XML Root element
    public function GetXMLRoot() : \DOMElement;

    // Method to get the XML current node
    public function GetXMLCurrentNode() : ?\DOMElement;

    // Method to get the XML current attribute of the current node
    public function GetXMLCurrentAttribute() : ?\DOMAttr;

    // Method to get XML attributes by value
    public function GetXMLAttributes(string $_attrValue = null) : ?array;

    // Method to get the active XPath query
    public function GetXMLCurrentXPathQuery() : ?string;

    // Method to get the children nodes of the current nodes
    public function GetXMLChildrenNodes() : ?\DOMNodeList;

    // Method to get a child node of the current node
    public function GetXMLChildNode(string $_nodeName = null, string $_attrName = null, string $_attrValue = null) : ?\DOMNode;

    // Method to read one attribute of the current node
    public function ReadXMLAttr(string $_attrName) : string;

    // Method to read the text node of the current node
    public function ReadXMLNodeText() : string;
}