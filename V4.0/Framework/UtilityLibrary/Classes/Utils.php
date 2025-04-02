<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the Jéoline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Utility\Classes;

header('Content-Type: text/html; charset=utf-8');

use TS_Configuration\Classes\AbstractCls;
use TS_Configuration\Classes\XMLManager;
use TS_Exception\Classes\UtilsException;

/*
 * Utils Abstract class
 */
class Utils extends AbstractCls
{
    // Method to construct the Utils class
    public function __construct(){
        // Set the Exception property
        $this->setException();
    }

    /* Inherited protected methods */

    // Method to set the exception property
    protected function setException() : void{
        $this->exception = new UtilsException();
    }

    // Method to get the exception property
    public function getException() : void{
        throw $this->exception;
    }

    /* Public methods */

    /** Get DB connection string */
    public function getDBConnectionData(XMLManager $_xml, string $PDODriver) : array{
        // Case insensivity
        $PDODriver = strtoupper($PDODriver);

        // Check whether the PDO driver is supported
        $xpath = '//dbConnection[@PDODriver="'.$PDODriver.'"]';
        $nodelist = $_xml->XMLPath($xpath);

        if(is_null($nodelist) || !$nodelist->count())
            throw new \InvalidArgumentException();

        // Check whether the PDO driver is uniquely documented in config
        if($nodelist->count() != 1)
            throw new \LogicException();

        return array($nodelist->item(0)->getAttribute('DSN'), $nodelist->item(0)->getAttribute('userName'),
            $nodelist->item(0)->getAttribute('passWord'));

    }

    /** Get Image function */
    public function getImage(XMLManager $_xml, string $_name) : string{
        $xpath = '//images/image[@name="'.$_name.'"]';
        $nodelist = $_xml->XMLPath($xpath);

        if (is_null($nodelist))
            throw new \LogicException();

        return $nodelist->item(0)->getAttribute('src');
    }

    public function getCompanyName(XMLManager $_xml) : string
    {
        $xpath = '//company';
        $nodelist = $_xml->XMLPath($xpath);

        if (is_null($nodelist))
            throw new \LogicException();

        return $nodelist->item(0)->getAttribute('name');
    }

    /** Get language function */
    public function getLanguage(XMLManager $_xml, string $_iso) : string{
        $xpath = '//languages/lang[@iso="'.$_iso.'"]';
        $nodelist = $_xml->XMLPath($xpath);

        if (is_null($nodelist))
            throw new \LogicException();

        return $nodelist->item(0)->getAttribute('name');
    }

    /** Get default language */
    public function getDefaultLanguage(XMLManager $_xml) : string{
        $xpath = '//languages/lang[@default="true"]';
        $nodelist = $_xml->XMLPath($xpath);

        if (is_null($nodelist))
            throw new \LogicException();

        return $nodelist->item(0)->getAttribute('iso');
    }

    /** Get default app */
    public function getDefaultApp(XMLManager $_xml) : \DOMNode{
        $xpath = '//applications/app[@default="true"]';
        $nodelist = $_xml->XMLPath($xpath);

        if (is_null($nodelist))
            throw new \LogicException();

        return $nodelist->item(0);
    }

    /** Get link paths */
    public function getPath(XMLManager $_xml, string $_type) : string{
        $xpath = '//links/link[@name="'.$_type.'Path"]';
        $nodelist = $_xml->XMLPath($xpath);

        if (is_null($nodelist))
            throw new \LogicException();

        return $nodelist->item(0)->getAttribute('path');
    }

    /** Set PHP session variable */
    public function setSession(?array $sessionArgs = null): void
    {
        if (empty($sessionArgs))
            return;

        foreach ($sessionArgs as $key => $value)
            $_SESSION[$key] = $value;
    }

    /** Get LAN IP Address */
    public function getIP() : string
    {
        return gethostbyname(gethostname());
    }
}