<?php

namespace API_BillingRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a record from the 'cl_Discounts' table.
 */
class Discount extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_Discounts';
}