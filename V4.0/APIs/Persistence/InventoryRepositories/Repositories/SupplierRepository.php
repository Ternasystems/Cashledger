<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\Suppliers;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\Supplier;
use Exception;
use TS_Utility\Enums\OrderEnum;

class SupplierRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Supplier
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Supplier ? $entity : null;
    }

    public function GetAll(): ?Suppliers
    {
        $collection = parent::GetAll();
        return $collection instanceof Suppliers ? $collection : null;
    }

    public function GetById(string $id): ?Supplier
    {
        $entity = parent::GetById($id);
        return $entity instanceof Supplier ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Suppliers
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Suppliers ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Supplier
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Supplier ? $entity : null;
    }

    public function OrderBy(Collectable $suppliers, array $properties, array $orderBy = [OrderEnum::ASC]): ?Suppliers
    {
        if (!$suppliers instanceof Suppliers)
            throw new Exception("Suppliers must be instance of Suppliers");

        $collection = parent::OrderBy($suppliers, $properties, $orderBy);
        return $collection instanceof Suppliers ? $collection : null;
    }
}