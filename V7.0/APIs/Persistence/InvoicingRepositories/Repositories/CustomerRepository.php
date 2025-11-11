<?php

namespace API_InvoicingRepositories;

use API_DTORepositories\Repository;
use API_InvoicingRepositories_Collection\Customers;
use API_InvoicingRepositories_Context\InvoicingContext;
use API_InvoicingRepositories_Model\Customer;

/**
 * @extends Repository<Customer, Customers>
 */
class CustomerRepository extends Repository
{
    public function __construct(InvoicingContext $context)
    {
        parent::__construct($context, Customer::class, Customers::class);
    }
}