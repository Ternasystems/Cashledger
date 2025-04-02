<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\Warehouses;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\Warehouse;
use Exception;
use TS_Utility\Enums\OrderEnum;

class WarehouseRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Warehouse
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Warehouse ? $entity : null;
    }

    public function GetAll(): ?Warehouses
    {
        $collection = parent::GetAll();
        return $collection instanceof Warehouses ? $collection : null;
    }

    public function GetById(string $id): ?Warehouse
    {
        $entity = parent::GetById($id);
        return $entity instanceof Warehouse ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Warehouses
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Warehouses ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Warehouse
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Warehouse ? $entity : null;
    }

    public function OrderBy(Collectable $warehouses, array $properties, array $orderBy = [OrderEnum::ASC]): ?Warehouses
    {
        if (!$warehouses instanceof Warehouses)
            throw new Exception("Warehouses must be instance of Warehouses");

        $collection = parent::OrderBy($warehouses, $properties, $orderBy);
        return $collection instanceof Warehouses ? $collection : null;
    }
}