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
interface iXMLWriter
{
    // Method to set the XML current node
    public function SetXMLCurrentNode(\DOMElement $_xmlNode) : void;

    // Method to set the current node one related attribute
    public function SetXMLCurrentAttribute(\DOMAttr $_xmlAttr) : void;

    // Method to add an attribute to the current XML node
    public function AddXMLAttr(string $_attrName, string $_attrValue = null) : \DOMAttr;

    // Method to edit one current XML node attribute
    public function EditXMLAttr(string $_attrName, string $_attrValue = null) : \DOMAttr;

    // Method to edit all current XML node attributes
    public function EditXMLAttrs(string $_attrValue) : bool;

    // Method to delete one current XML node attribute
    public function DelXMLAttr(string $_attrName) : \DOMNode;

    // Method to add a child node to the current XML node
    public function AddXMLNode(string $_nodeName, string $_nodeText = null) : \DOMElement;

    // Method to edit the current node
    public function EditXMLNode(string $_nodeText) : \DOMText;

    // Method to delete a child node of the current node or the current node itself
    public function DelXMLNode(\DOMNode $_xmlNode = null) : ?\DOMNode;
}