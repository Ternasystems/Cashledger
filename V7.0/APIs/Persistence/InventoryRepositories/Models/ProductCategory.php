<?php

namespace API_InventoryRepositories_Model;

use API_DTORepositories_Model\DTOBase;

/**
 * Represents a record from the 'cl_ProductCategories' table.
 */
class ProductCategory extends DTOBase
{
    /**
     * The database table name for this model.
     * @var string
     */
    protected static string $table = 'cl_ProductCategories';
}