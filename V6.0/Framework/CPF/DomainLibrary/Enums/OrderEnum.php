<?php
// FILE: Framework/DomainLibrary/Enums/OrderEnum.php

namespace TS_Domain\Enums;

/**
 * Specifies the direction for a sorting operation.
 */
enum OrderEnum: string
{
    /** Sorts in ascending order (A-Z, 1-9). */
    case ASC = 'ASC';

    /** Sorts in descending order (Z-A, 9-1). */
    case DESC = 'DESC';

    /**
     * Defines a custom sorting order provided by a user-defined comparison closure.
     * For example, to group items by a certain logic (e.g., evens before odds)
     * and then sort each group.
     */
    case MIXED = 'MIXED';
}