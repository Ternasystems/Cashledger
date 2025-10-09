<?php

declare(strict_types=1);

namespace TS_Exception\Classes;

/**
 * Represents an error that occurs within the domain logic, such as
 * a DSL parsing failure or an invalid business rule.
 */
class DSLException extends AbstractException
{
    // All logic is inherited from the parent AbstractException.
    // This class exists to allow for specific catching, e.g., catch (DSLException $e)
}