<?php

declare(strict_types=1);

namespace TS_Exception\Classes;

/**
 * Represents an error that occurs during service registration or resolution
 * in the dependency injection container.
 */
class DIException extends AbstractException
{
    // All logic is inherited from the parent AbstractException.
    // This class exists to allow for specific catching, e.g., catch (DIException $e)
}