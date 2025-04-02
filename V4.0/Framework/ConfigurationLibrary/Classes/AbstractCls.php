<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the J�oline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Configuration\Classes;

header('Content-Type: text/html; charset=utf-8');

use TS_Exception\Classes\AbstractException;

/*
 * AbstractCls class
 */
abstract class AbstractCls
{
    /* Protected properties */
    protected AbstractException $exception;

    /* Protected methods */

    /** Protected abstract methods */

    // Method to set the exception property
    abstract protected function SetException() : void;

    // Method to get the exception property
    abstract protected function GetException() : void;
}