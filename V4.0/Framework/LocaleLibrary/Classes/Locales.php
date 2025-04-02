<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the Jéoline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Locale\Classes;

header('Content-Type: text/html; charset=utf-8');

use TS_Configuration\Classes\AbstractCls;
use TS_Configuration\Classes\XMLManager;
use TS_Exception\Classes\UtilsException;

/*
 * Locales Abstract class
 */

class Locales extends AbstractCls
{
    // Method to construct the Locales class
    public function __construct()
    {
        // Set the Exception property
        $this->setException();
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

    /* Public methods */

    /** Get locale for a specific name, action and application */
    public function getLocale(XMLManager $_xml, string $_lang, string $_app, string $_action, string $_name): string
    {
        $xpath = '//' . $_lang . '/item[@app="' . $_app . '"][@action="' . $_action . '"][@name="' . $_name . '"]';
        $nodelist = $_xml->XMLPath($xpath);

        if (is_null($nodelist))
            throw new \LogicException();

        return $nodelist->item(0)->textContent;
    }

    public function getComponentLocale(XMLManager $_xml, string $_lang, string $_name, string $_value): string
    {
        $xpath = '//' . $_lang . '/component[@name="' . $_name . '"][@value="' . $_value . '"]';
        $nodelist = $_xml->XMLPath($xpath);

        if (is_null($nodelist))
            throw new \LogicException();

        return $nodelist->item(0)->textContent;
    }
}