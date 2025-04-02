<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\Customers;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\Customer;
use Exception;
use TS_Utility\Enums\OrderEnum;

class CustomerRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Customer
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Customer ? $entity : null;
    }

    public function GetAll(): ?Customers
    {
        $collection = parent::GetAll();
        return $collection instanceof Customers ? $collection : null;
    }

    public function GetById(string $id): ?Customer
    {
        $entity = parent::GetById($id);
        return $entity instanceof Customer ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Customers
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Customers ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Customer
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Customer ? $entity : null;
    }

    public function OrderBy(Collectable $customers, array $properties, array $orderBy = [OrderEnum::ASC]): ?Customers
    {
        if (!$customers instanceof Customers)
            throw new Exception("Customers must be instance of Customers");

        $collection = parent::OrderBy($customers, $properties, $orderBy);
        return $collection instanceof Customers ? $collection : null;
    }
}