<?php

declare(strict_types=1);

namespace TS_Configuration\Classes;

/**
 * Abstract base class for the framework.
 *
 * This class serves as the foundation for other classes within the Hypergates framework,
 * powered by Ternary Data Systems.
 */
abstract class AbstractCls
{
    // By removing the abstract methods and properties, this class now serves as a clean,
    // empty base class. We can add shared functionality here later if needed.
    //
    // Child classes should handle their own exceptions by throwing them, for example:
    //
    // if ($error) {
    //     throw new \TS_Exception\Classes\SomeSpecificException("An error occurred.");
    // }
}