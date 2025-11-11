<?php

namespace API_BillingRepositories_model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a record from the 'cl_Prices' table.
 */
class Price extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_Prices';
}