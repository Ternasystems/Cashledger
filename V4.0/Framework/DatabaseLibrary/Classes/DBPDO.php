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
use PDOException;
use PDOStatement;
use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\DBException;

/*
 * DBPDO class
 */
abstract class DBPDO extends AbstractCls
{
    // Private properties
    private string $dbstmt;
    private ?PDOStatement $dbprepared = null;
    private int $rowCount;

    // Constructor
    public function __construct(string $_stmt = ''){
        // Set the Exception property
        $this->SetException();

        // Store the statement
        $this->dbstmt = $_stmt;
    }

    /* Inherited protected methods */

    // Method to set the exception property
    protected function SetException() : void{
        $this->exception = new DBException();
    }

    // Method to get the exception property
    public function GetException() : void{
        throw $this->exception;
    }

    // Method to prepare the statement
    protected function PreparePDO(PDO $_pdo) : void{
        try{
            $this->dbprepared = $_pdo->prepare($this->dbstmt);
        }catch(PDOException $e){
            $messages = array(
                'en' => 'An error occured during the preparation of the PDO statement '.$e->getMessage(),
                'fr' => 'Une erreur est survenue pendant la préparation de la requête PDO '.$e->getMessage()
            );

            $this->exception->SetException(new PDOException());
            $this->exception->SetMessages($messages);

            $this->GetException();
        }
    }

    // Method to execute the prepared statement
    protected function ExecutePDO(?array $_arg = null, ?array $_options = null) : PDOStatement{
        if (is_null($_options))
            $this->dbprepared->execute($_arg);
        else{
            foreach ($_arg as $key => $item) {
                if (key_exists($key, $_options))
                    $this->dbprepared->bindValue($key, $item, $_options[$key]);
                else
                    $this->dbprepared->bindValue($key, $item);
            }
            $this->dbprepared->execute();
        }

        $this->rowCount = $this->dbprepared->rowCount();
        return $this->dbprepared;
    }

    // Method to execute the statement and fetch the results
    protected function DBReturn(bool $_bool, PDO $_pdo, ?array $_arg = null, int $_fetchMode = 0, ?array $_options = null) : array{
        $result = array();
        if($_bool){
            $this->PreparePDO($_pdo);
            $this->ExecutePDO($_arg, $_options);

            // Check the fetch style
            if($this->rowCount){
                switch($_fetchMode){
                    case 1:
                        {
                            $result = $this->dbprepared->fetchAll(PDO::FETCH_ASSOC);
                        }
                        break;
                    case 2:
                        {
                            $result = $this->dbprepared->fetchAll(PDO::FETCH_NUM);
                        }
                        break;
                    default:
                    {
                        $result = $this->dbprepared->fetchAll(PDO::FETCH_BOTH);
                    }
                }
            }
        }
        return $result;
    }

    // Method to execute the statement and not fetch the results
    protected function DBNoReturn(bool $_bool, PDO $_pdo, ?array $_arg = null, ?array $_options = null): void{
        if($_bool){
            $this->PreparePDO($_pdo);
            $this->ExecutePDO($_arg, $_options);
        }
    }

    // Method to get the current statement
    public function GetStatement() : string{
        return $this->dbstmt;
    }

    // Method to get the row count
    public function GetRowCount() : int{
        return $this->rowCount;
    }

    // Method to close the cursor
    public function ClosePDO() : bool{
        return $this->dbprepared == null || $this->dbprepared->closeCursor();
    }

    // Method to replace the current statement
    public function SetStatement(string $_stmt) : void{
        // Close the cursor
        if(!$this->ClosePDO())
            $this->ClosePDO();

        $this->dbstmt = $_stmt;
    }

    // Method to parse, prepare and execute the statement
    public abstract function QueryPDO(PDO $_pdo, ?array $_arg = null, int $_fetchMode = 0, ?array $_options = null);
}