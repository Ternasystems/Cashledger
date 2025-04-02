<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the Jéoline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Exception\Classes;

use Exception;

header('Content-Type: text/html; charset=utf-8');

/*
 * UtilsException class
 */
class UtilsException extends AbstractException
{

    /* Inherited public method */

    /**
     * @throws Exception
     */
    public function SetMessages(array $_messages) : void{
        // Check if indexes in the argument are of type string
        $indexes = array_unique(array_keys($_messages), SORT_STRING);
        foreach($indexes as $index){
            if(!is_string($index) || strlen($index) != 2){
                $msg = 'A UtilsException method (SetMessages) received an array with non-string keys. Exception messages array must be a key/value array where keys are all of type string(2)';
                $this->InternalException($msg);
            }
        }

        // Change type of the array values to string
        foreach($_messages as $key => $value){
            $_messages[$key] = $this->StringMessage($value);
            $this->SetLang($key);
        }

        // Set the property
        $this->messages = $_messages;
    }

    // Method to set the message for a specific language

    /**
     * @throws Exception
     */
    public function SetMessage(string $_lang, string $_message) : void{
        // Check if lang is a 2 characters string
        if(strlen($_lang) != 2){
            $msg = 'A UtilsException method (SetMessage) received a wrong argument (lang). Lang argument must be of type string(2).';
            $this->InternalException($msg);
        }

        // Change the type of the message parameter
        $_message = $this->StringMessage($_message);

        // Set the property
        $this->messages[$_lang] = $_message;
        $this->SetLang($_lang);
    }

    // Method to get the message for a specific language

    /**
     * @throws Exception
     */
    public function GetMessageValue(string $_lang) : string{
        // Check if lang is a 2 characters string and exists in the property indexes
        if(strlen($_lang) != 2 || !array_key_exists($_lang, $this->messages)){
            $msg = 'A UtilsException method (getMessageValue) received a wrong argument (lang). Lang argument must be of type string(2) and of one of the following value: '.join(',', array_keys($this->messages));
            $this->InternalException($msg);
        }

        // Return the Exception message
        return 'UtilsException ('.get_class($this->exception).'): '.$this->messages[$_lang];
    }
}