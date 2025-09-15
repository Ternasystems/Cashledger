<?php

namespace API_ProfilingRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a type of contact (e.g., Email, Phone, Address).
 */
class ContactType extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_ContactTypes';
}