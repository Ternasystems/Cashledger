<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the Jéoline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Database\Classes;

header('Content-Type: text/html; charset=utf-8');

use PDO;
use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\DBException;


/*
 * DBConnection class
 */
class DBConnection extends AbstractCls
{
    // Static properties
    private static PDO $dbpdo;

    // Method to construct the DBConnection class
    public function __construct(){
        // Set the Exception property
        $this->SetException();
    }

    /* Inherited protected method */

    // Method to set the exception property
    protected function SetException() : void{
        $this->exception = new DBException();
    }

    // Method to get the exception property
    public function GetException() : void{
        throw $this->exception;
    }

    // Static method to establish a database connection
    public static function PDOConnection(string $_dsn, string $_username, string $_userpassword) : void{
        // Establish a database connection
        self::$dbpdo = new PDO($_dsn, $_username, $_userpassword, array(PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
    }

    // Method to get the static property
    public function GetPDO() : PDO{
        return self::$dbpdo;
    }
}