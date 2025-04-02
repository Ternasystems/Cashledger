<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the J�oline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Configuration\Classes;

header('Content-Type: text/html; charset=utf-8');

use TS_Exception\Classes\XMLException;

/*
 * AbstractXMLManager class
 */
abstract class AbstractXMLManager extends AbstractCls
{
    /* Protected properties */
    protected ?string $xmlFilePath = null;
    protected ?\DOMDocument $xmlObj = null;
    protected ?\DOMElement $xmlRoot = null;
    protected ?\DOMElement $xmlNode = null;
    protected ?\DOMNamedNodeMap $xmlAttrs = null;
    protected ?\DOMAttr $xmlAttr = null;
    protected ?\DOMXPath $xmlXPathObj = null;
    protected ?string $xmlXPathQuery = null;
    protected string $lang = 'en';

    /* Inherited protected methods */

    // Method to set the exception property
    protected function SetException() : void{
        $this->exception = new XMLException();
    }

    // Method to get the exception property
    public function GetException() : void{
        throw $this->exception;
    }

    /* Public methods */

    // Method to save XML data back to file
    public function SaveXML() : bool{
        $this->xmlObj->preserveWhiteSpace = false;
        $this->xmlObj->formatOutput = true;

        // Return a boolean to state if the document has been successfully saved
        return (is_int($this->xmlObj->save($this->xmlFilePath)));
    }

    // Method to set lang property
    public function SetLang(string $_lang) : void{
        // Check if the lang argument is a 2 characters string
        if(strlen($_lang) != 2){
            $messages = array(
                'en' => 'The language parameter must be of type string(2).',
                'fr' => 'Le paramètre de langue doit être du type string(2).'
            );

            $this->exception->SetException(new \InvalidArgumentException());
            $this->exception->SetMessages($messages);

            $this->GetException();
        }

        // Set the property
        $this->lang = $_lang;
    }

    // Method to get lang property
    public function GetLang() : string{
        return $this->lang;
    }

    // Method to check if the current node is the root node
    public function IsRoot() : bool{
        return $this->xmlNode->isSameNode($this->xmlRoot);
    }

    // Method to set XML to root node
    public function SetXMLToRoot() : void{
        // Set XML to Root node
        $this->xmlNode = $this->xmlRoot;

        // Reset related attributes to null
        $this->xmlAttr = $this->xmlAttrs = null;

        // Set related attributes
        if($this->xmlRoot->hasAttributes())
            $this->xmlAttrs = $this->xmlRoot->attributes;
    }

    // Method to set XML to the first node
    public function SetXMLToFirstNode() : void{
        // Check if current node has children and set XML to first node if any
        if($this->xmlNode->hasChildNodes())
            $this->xmlNode = $this->xmlNode->firstChild;
    }

    // Method to set XML to the last node
    public function SetXMLToLastNode() : void{
        // Check if current node has children and set XML to last node if any
        if($this->xmlNode->hasChildNodes())
            $this->xmlNode = $this->xmlNode->lastChild;
    }

    // Method to execute XPath queries
    public function XMLPath(string $_XPath) : ?\DOMNodeList{
        // Store the XPath query
        $this->xmlXPathQuery = $_XPath;

        // Check the validity of the XPath query
        if(!($nodelist = $this->xmlXPathObj->query($this->xmlXPathQuery))){
            $messages = array(
                'en' => 'XPath query did not execute properly. Check your query: <b>'.$this->xmlXPathQuery.'</b>.',
                'fr' => 'La requête XPath n\'a pas été executé correctement. Vérifiez votre requête: <b>'.$this->xmlXPathQuery.'</b>.'
            );

            $this->exception->SetException(new \InvalidArgumentException());
            $this->exception->SetMessages($messages);

            throw $this->exception;
        }

        return ($nodelist->length) ? $nodelist : null;
    }

    /** Public abstract methods */

    // Method to check if a node exists in the current node hierarchy
    abstract public function HasNode(string $_xmlNodeName) : int;
}