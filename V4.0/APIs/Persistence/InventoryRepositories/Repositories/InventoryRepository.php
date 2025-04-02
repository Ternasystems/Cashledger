<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\Inventories;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\Inventory;
use Exception;
use TS_Utility\Enums\OrderEnum;

class InventoryRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Inventory
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Inventory ? $entity : null;
    }

    public function GetAll(): ?Inventories
    {
        $collection = parent::GetAll();
        return $collection instanceof Inventories ? $collection : null;
    }

    public function GetById(string $id): ?Inventory
    {
        $entity = parent::GetById($id);
        return $entity instanceof Inventory ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Inventories
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Inventories ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Inventory
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Inventory ? $entity : null;
    }

    public function OrderBy(Collectable $inventories, array $properties, array $orderBy = [OrderEnum::ASC]): ?Inventories
    {
        if (!$inventories instanceof Inventories)
            throw new Exception("Inventories must be instance of Inventories");

        $collection = parent::OrderBy($inventories, $properties, $orderBy);
        return $collection instanceof Inventories ? $collection : null;
    }
}