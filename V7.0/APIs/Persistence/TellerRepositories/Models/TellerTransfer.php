<?php

namespace API_TellerRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a record from the 'cl_TellerTransfers' table.
 */
class TellerTransfer extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_TellerTransfers';
}