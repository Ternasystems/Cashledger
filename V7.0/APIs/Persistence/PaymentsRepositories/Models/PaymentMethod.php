<?php

namespace API_PaymentsRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a record from the 'cl_PaymentMethods' table.
 */
class PaymentMethod extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_PaymentMethods';
}