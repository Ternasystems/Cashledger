<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the Jéoline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Exception\Classes;

use Exception;
use Throwable;

header('Content-Type: text/html; charset=utf-8');

/*
 * AbstractException class
 */
abstract class AbstractException extends Exception
{
    /* Properties */

    /** Protected properties */
    protected ?Exception $exception = null;
    protected array $messages = array();
    protected array $lang = array();

    /* Methods */

    /** Protected methods
     * @throws Exception
     */

    // Method to throw an internal exception
    protected function InternalException(string $_message) : void{
        throw new Exception('This is an internal exception caused by '.$_message);
    }

    // Method to call or turn a non-string message to string
    protected function StringMessage(mixed $_message) : string{

        try{
            $msg = strval($_message);
        }catch(Throwable){
            $msg = var_dump($_message);
        }

        return $msg;
    }

    // Method to store exception languages
    protected function SetLang(string $_lang) : void{
        //Check if lang exists
        if(!in_array($_lang, $this->lang))
            $this->lang[] = $_lang;
    }

    /** Public methods
     * @throws Exception
     */

    // Method to send an internal exception
    public function SendException(string $_message) : void{
        $this->InternalException($_message);
    }

    // Method to set the exception property
    public function SetException(Exception $_exception) : void{
        $this->exception = $_exception;
    }

    // Method to get the exception property
    public function GetException() : Exception{
        return $this->exception;
    }

    // Method to get lang array
    public function GetLang() : array{
        return $this->lang;
    }

    /*** Public abstract methods */

    // Method to set the messages property
    abstract public function SetMessages(array $_messages) : void;

    // Method to set the message for a specific language
    abstract public function SetMessage(string $_lang, string $_message) : void;

    // Method to get the message for a specific language
    abstract public function GetMessageValue(string $_lang) : string;
}