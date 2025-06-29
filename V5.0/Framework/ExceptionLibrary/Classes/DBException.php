<?php

declare(strict_types=1);

namespace TS_Exception\Classes;

/**
 * Represents an error that occurs during a database operation.
 */
class DBException extends AbstractException
{
    // All logic is now handled by the parent AbstractException.
    // This class exists to allow for specific catching, e.g., catch (DBException $e)
}