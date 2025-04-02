<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the J�oline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Configuration\Classes;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMException;
use DOMNode;
use DOMNodeList;
use DOMText;
use DOMXPath;
use ErrorException;
use InvalidArgumentException;
use TS_Configuration\Interfaces\iXMLReader;
use TS_Configuration\Interfaces\iXMLWriter;

header('Content-Type: text/html; charset=utf-8');

/*
 * XMLManager class
 */
class XMLManager extends AbstractXMLManager implements iXMLReader, iXMLWriter
{
    /* Constructor */

    /**
     * @throws DOMException
     */
    public function __construct(string $_filepath, string $_rootElement = null){
        // Set the Exception property
        $this->SetException();

        // Check the filepath argument validity
        if(file_exists($_filepath) && pathinfo($_filepath, PATHINFO_EXTENSION) !== 'xml'){
            $messages = array(
                'en' => 'XML file required, sent <b>'.strtoupper(pathinfo($_filepath, PATHINFO_EXTENSION)).'</b> instead.',
                'fr' => 'Fichier XML requis, vous avez plutôt envoyé <b>'.strtoupper(pathinfo($_filepath, PATHINFO_EXTENSION)).'</b>.'
            );

            $this->exception->SetException(new InvalidArgumentException());
            $this->exception->SetMessages($messages);

            $this->GetException();
        }

        // Check the rootElement argument validity
        $this->xmlFilePath = $_filepath;
        if(!file_exists($this->xmlFilePath) && is_null($_rootElement)){
            $messages = array(
                'en' => 'Bad file path or root element is null.',
                'fr' => 'Chemin vers le fichier erroné ou racine nulle.'
            );

            $this->exception->SetException(new InvalidArgumentException());
            $this->exception->SetMessages($messages);

            $this->GetException();
        }

        // Create an XML document object
        if(!file_exists($this->xmlFilePath)){
            // Create a DOMDocument
            $this->xmlObj = new DOMDocument('1.0', 'UTF-8');

            // Create the root element
            $this->xmlRoot = $this->xmlObj->createElement($_rootElement);
            $this->xmlObj->appendChild($this->xmlRoot);

            // Save the XML document
            if(!$this->SaveXML()){
                $messages = array(
                    'en' => 'Could not create the XML file.',
                    'fr' => 'Création du fichier XML impossible.'
                );

                $this->exception->SetException(new InvalidArgumentException());
                $this->exception->SetMessages($messages);

                $this->GetException();
            }
        }else{
            // Load the XML file
            $this->xmlObj = new DOMDocument();
            $this->xmlObj->load($this->xmlFilePath);

            // Set the root property
            $this->xmlRoot = $this->xmlObj->documentElement;
        }

        // Set the current node
        $this->xmlNode = $this->xmlRoot;

        // Set the current node attributes
        $this->xmlAttrs = $this->xmlNode->attributes;

        // Set the DOMXPath object
        $this->xmlXPathObj = new DOMXPath($this->xmlObj);
    }

    /** Inherited from AbstractXMLManager */

    // Method to check if a node exists in the current node hierarchy
    public function HasNode(string $_xmlNodeName) : int{
        //Initialize variables
        $query = '//'.$this->xmlNode->nodeName.'//'.$_xmlNodeName;

        return is_null($nodes = $this->XMLPath($query)) ? 0 : $nodes->length;
    }

    /* Implemented methods */

    /** iXMLWriter interface methods */

    // Method to set the XML current node
    public function SetXMLCurrentNode(DOMElement $_xmlNode) : void{
        // Get the parent node
        $parent = $_xmlNode->parentNode;

        // Get the attributes
        $attr = ($_xmlNode->hasAttributes()) ? $_xmlNode->attributes : null;

        // Initialize XPath query
        $query = '//'.$parent->nodeName;
        if($parent->hasAttributes()){
            foreach($parent->attributes as $parentAttr)
                $query .= '[@'.$parentAttr->name.(empty($parentAttr->value) ? ']' : '="'.$parentAttr->value.'"]');
        }
        $query .= '//'.$_xmlNode->nodeName;

        if(!is_null($attr)){
            foreach($attr as $value)
                $query .= '[@'.$value->name.(empty($value->value) ? ']' : '="'.$value->value.'"]');
        }

        // Execute the XPath
        $node = $this->XMLPath($query);

        // Set the current node
        if($node->length == 1){
            $this->xmlNode = $node->item(0);
            $this->xmlAttrs = $attr;
        }
    }

    // Method to set the current node one related attribute
    public function SetXMLCurrentAttribute(DOMAttr $_xmlAttr) : void{
        // Check if the attribute belongs to the current node
        $this->xmlAttr = $this->xmlAttrs->getNamedItem($_xmlAttr->nodeName);

        // Set the current attribute
        if(!is_null($_xmlAttr))
            $this->xmlAttr = $_xmlAttr;
    }

    // Method to add an attribute to the current XML node
    public function AddXMLAttr(string $_attrName, $_attrValue = null) : DOMAttr{
        // Check if the attribute exists on the current node
        if($this->xmlNode->hasAttribute($_attrName)){
            $messages = array(
                'en' => 'Attribute <b>'.$_attrName.'</b> already exists for this node.',
                'fr' => 'L\'attribut <b>'.$_attrName.'</b> existe déjà à ce noeud.'
            );

            $this->exception->SetException(new InvalidArgumentException());
            $this->exception->SetMessages($messages);

            $this->GetException();
        }

        // Create the new attribute
        $attr = $this->xmlNode->setAttribute($_attrName, $_attrValue);
        $this->xmlAttrs = $this->xmlNode->attributes;

        // Save the XML file
        $this->SaveXML();

        return $this->xmlAttr = $attr;
    }

    // Method to edit one current XML node attribute
    public function EditXMLAttr(string $_attrName, $_attrValue = null) : DOMAttr{
        // Check if the attribute does not exist on the current node
        if(!$this->xmlNode->hasAttribute($_attrName)){
            $messages = array(
                'en' => 'Attribute <b>'.$_attrName.'</b> does not exist for this node.',
                'fr' => 'L\'attribut <b>'.$_attrName.'</b> n\'existe pas à ce noeud.'
            );

            $this->exception->SetException(new InvalidArgumentException());
            $this->exception->SetMessages($messages);

            $this->GetException();
        }

        // Edit the attribute
        $attr = $this->xmlNode->setAttribute($_attrName, $_attrValue);

        // Save the XML file
        $this->SaveXML();

        return $this->xmlAttr = $attr;
    }

    // Method to edit all current XML node attributes
    public function EditXMLAttrs(string $_attrValue) : bool{
        // Check if current nodes has attributes
        if (!$this->xmlNode->hasAttributes())
            return false;

        foreach ($this->xmlAttrs as $attr)
            $this->EditXMLAttr($attr, $_attrValue);

        return true;
    }

    // Method to delete one current XML node attribute
    public function DelXMLAttr(string $_attrName) : DOMNode{
        // Check if the attribute does not exist on the current node
        if(!$this->xmlNode->hasAttribute($_attrName)){
            $messages = array(
                'en' => 'Attribute <b>'.$_attrName.'</b> does not exists for this node.',
                'fr' => 'L\'attribut <b>'.$_attrName.'</b> n\'existe pas à ce noeud.'
            );

            $this->exception->SetException(new InvalidArgumentException());
            $this->exception->SetMessages($messages);

            $this->GetException();
        }

        // Get the attribute
        $attr = $this->xmlAttrs->getNamedItem($_attrName);

        // Delete attribute
        $this->xmlNode->removeAttribute($_attrName);
        $this->xmlAttrs = $this->xmlNode->attributes;

        if($this->xmlAttr->nodeName = $_attrName)
            $this->xmlAttr = null;

        // Save the XML file
        $this->saveXML();

        return $attr;
    }

    // Method to add a child node to the current XML node

    /**
     * @throws DOMException
     */
    public function AddXMLNode(string $_nodeName, $_nodeText = null) : DOMElement{
        // Create the new node element
        $element = $this->xmlObj->createElement($_nodeName);

        // Create a text node if necessary
        if(!is_null($_nodeText)){
            $textNode = $this->xmlObj->createTextNode($_nodeText);

            // Append the text node to the new node element
            $element->appendChild($textNode);
        }

        // Append the new node element to the current node tree
        $this->xmlNode->appendChild($element);

        // Save the new node to the DOM
        $this->saveXML();

        // Set the new node as current node
        $this->xmlNode = $element;
        $this->xmlAttr = $this->xmlAttrs = null;

        return $element;
    }

    // Method to edit the current node
    public function EditXMLNode(string $_nodeText) : DOMText{
        // Create a text node
        $textNode = $this->xmlObj->createTextNode($_nodeText);

        // Check if there is at least one text node in the current node tree
        if(!is_null($nodeList = $this->GetXMLChildrenNodes())){
            foreach($nodeList as $value){
                // Remove any existing text node
                if($value->nodeType == 3)
                    $this->xmlNode->removeChild($value);
            }
        }

        // Append the new text node to the current node tree
        $this->xmlNode->appendChild($textNode);

        // Save the new text node to the DOM
        $this->saveXML();

        return $textNode;
    }

    // Method to delete a child node of the current node or the current node itself
    public function DelXMLNode($_xmlNode = null) : ?DOMNode{
        // Get node name
        $nodeName = is_null($_xmlNode) ? $this->xmlNode->nodeName : $_xmlNode->nodeName;

        // Check that node is not root
        if($this->xmlRoot->nodeName == $nodeName){
            $messages = array(
                'en' => 'You tried to delete the root node. That operation is not allowed.',
                'fr' => 'Vous avez tenté de supprimer le noeud racine. Cette opération n\'est pas autorisée.'
            );

            $this->exception->setException(new ErrorException());
            $this->exception->setMessages($messages);

            $this->GetException();
        }

        // Check if node is child of the current node
        if(!is_null($_xmlNode) && $this->xmlNode->nodeName != $_xmlNode->parentNode->nodeName){
            $messages = array(
                'en' => 'The node you tried to delete is not related to the current node <b>'.$this->xmlNode->nodeName.'</b>, but to the node <b>'.$_xmlNode->parentNode->nodeName.'</b>',
                'fr' => 'Le noeud que vous essayez de supprimer n\'est pas lié au noeud courant <b>'.$this->xmlNode->nodeName.'</b>, mais plut�t au noeud <b>'.$_xmlNode->parentNode->nodeName.'</b>'
            );
            $this->exception->setException(new ErrorException());
            $this->exception->setMessages($messages);

            $this->GetException();
        }

        $node = $_xmlNode;
        if(is_null($_xmlNode)){
            $node = $this->xmlNode;
            $this->xmlNode = $this->xmlNode->parentNode;
        }

        $node = $this->xmlNode->removeChild($node);

        $this->saveXML();

        return $node;
    }

    /** iXMLReader interface methods */

    // Method to get the XML file name
    public function GetXMLFileName() : string{
        return $this->xmlFilePath;
    }

    // Method to get the XML DOM Object
    public function GetXMLDOM() : DOMDocument{
        return $this->xmlObj;
    }

    // Method to get the XPath object
    public function GetXMLXPath() : DOMXPath{
        return $this->xmlXPathObj;
    }

    // Method to get the XML Root element
    public function GetXMLRoot() : DOMElement{
        return $this->xmlRoot;
    }

    // Method to get the XML current node
    public function GetXMLCurrentNode() : ?DOMElement{
        return $this->xmlNode;
    }

    // Method to get the XML current attribute of the current node
    public function GetXMLCurrentAttribute() : ?DOMAttr{
        return $this->xmlAttr;
    }

    // Method to get XML attributes by value
    public function GetXMLAttributes(string $_attrValue = null) : ?array{
        // Check if current node has attributes
        if (!$this->xmlNode->hasAttributes())
            return null;

        // Check if value is null
        $arr = array();
        if (is_null($_attrValue)){
            foreach ($this->xmlAttrs as $attr)
                $arr[] = $attr;

            return $arr;
        }

        // Get attributes with value
        foreach ($this->xmlAttrs as $attr){
            if ($attr->nodeValue == $_attrValue)
                $arr[] = $attr;
        }

        return count($arr) > 0 ? $arr : null;
    }

    // Method to get the active XPath query
    public function GetXMLCurrentXPathQuery() : ?string{
        return $this->xmlXPathQuery;
    }

    // Method to get the children nodes of the current nodes
    public function GetXMLChildrenNodes() : ?DOMNodeList{
        // Check if the current node has child nodes
        return (!$this->xmlNode->hasChildNodes()) ? null : $this->xmlNode->childNodes;
    }

    // Method to get a child node of the current node
    public function GetXMLChildNode($_nodeName = null, $_attrName = null, $_attrValue = null) : ?DOMNode{
        // Check the validity of the arguments
        if(is_null($_nodeName) && is_null($_attrName)){
            $messages = array(
                'en' => 'Both nodeName and attrName cannot be null at the same time. Fill at least one of them.',
                'fr' => 'Les variables nodeName et attrName ne peuvent pas être nulles en même temps. Renseignez au moins une des deux variables.'
            );
            $this->exception->setException(new InvalidArgumentException());
            $this->exception->setMessages($messages);

            $this->GetException();
        }

        // Get the parent node
        $query = '//'.$this->xmlNode->nodeName;
        // Attach parent node attributes
        if($this->xmlNode->hasAttributes()){
            foreach($this->xmlNode->attributes as $attr)
                $query .= '[@'.$attr->name.(empty($attr->value) ? ']' : '="'.$attr->value.'"]');
        }

        // Check if the node name is not null
        $query .= '//'.(is_null($_nodeName) ? '*' : $_nodeName);
        // Check if the attribute and its value are not null
        $query .= is_null($_attrName) ? '' : '[@'.$_attrName.(is_null($_attrValue) ? ']' : '="'.$_attrValue.'"]');

        // Execute the XPath
        $node = $this->XMLPath($query);

        // Check the length of the DOMNodeList
        if(!is_null($node) && $node->length > 1){
            $messages = array(
                'en' => 'You can only select one node at a time. Please narrow your query.',
                'fr' => 'Vous ne pouvez sélectionner qu\'un noeud à la fois. Veuillez affiner votre requête.'
            );
            $this->exception->setException(new ErrorException());
            $this->exception->setMessages($messages);

            $this->GetException();
        }

        return is_null($node) ? null : $node->item(0);
    }

    // Method to read one attribute of the current node
    public function ReadXMLAttr(string $_attrName) : string{
        // Check if the current node has the attribute
        if(!$this->xmlNode->hasAttribute($_attrName)){
            $messages = array(
                'en' => 'The attribute <b>'.$_attrName.'</b> does not exists for this node.',
                'fr' => 'L\'attribut <b>'.$_attrName.'</b> n\'existe pas à ce noeud.'
            );
            $this->exception->setException(new InvalidArgumentException());
            $this->exception->setMessages($messages);

            $this->GetException();
        }

        // Set the attribute
        $this->xmlAttr = $this->xmlAttrs->getNamedItem($_attrName);
        return $this->xmlAttr->value;
    }

    // Method to read the text node of the current node
    public function ReadXMLNodeText() : string{
        $str = '';
        // Check if there is at least one text node in the current node tree
        if(!is_null($nodeList = $this->getXMLChildrenNodes())){
            foreach($nodeList as $value){
                // Get text node value
                if($value->nodeType == 3)
                    $str .= $value->wholeText.' ';
            }
        }

        return trim($str);
    }
}