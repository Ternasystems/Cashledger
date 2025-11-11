<?php

namespace API_TellerRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a record from the 'cl_TellerCashCounts' table.
 */
class TellerCashCount extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_TellerCashCounts';
}