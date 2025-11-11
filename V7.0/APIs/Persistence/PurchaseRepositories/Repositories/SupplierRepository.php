<?php

namespace API_PurchaseRepositories;

use API_DTORepositories\Repository;
use API_PurchaseRepositories_Collection\Suppliers;
use API_PurchaseRepositories_Context\PurchaseContext;
use API_PurchaseRepositories_Model\Supplier;

/**
 * @extends Repository<Supplier, Suppliers>
 */
class SupplierRepository extends Repository
{
    public function __construct(PurchaseContext $context)
    {
        parent::__construct($context, Supplier::class, Suppliers::class);
    }
}