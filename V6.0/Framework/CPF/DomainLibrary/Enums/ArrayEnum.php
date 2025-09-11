<?php
// FILE: Framework/DomainLibrary/Enums/ArrayEnum.php

namespace TS_Domain\Enums;

/**
 * Specifies the desired format for array conversion.
 */
enum ArrayEnum
{
    /** An indexed array of objects. [0 => object, 1 => object] */
    case Indexed;
    /** An associative array, requires a key selector. ['key1' => object, 'key2' => object] */
    case Associative;
    /** An array of arrays, with each object converted to an array. */
    case Complex;
}