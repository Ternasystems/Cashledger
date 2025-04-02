<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\Units;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\Unit;
use Exception;
use TS_Utility\Enums\OrderEnum;

class UnitRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Unit
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Unit ? $entity : null;
    }

    public function GetAll(): ?Units
    {
        $collection = parent::GetAll();
        return $collection instanceof Units ? $collection : null;
    }

    public function GetById(string $id): ?Unit
    {
        $entity = parent::GetById($id);
        return $entity instanceof Unit ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Units
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Units ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Unit
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Unit ? $entity : null;
    }

    public function OrderBy(Collectable $units, array $properties, array $orderBy = [OrderEnum::ASC]): ?Units
    {
        if (!$units instanceof Units)
            throw new Exception("Units must be instance of Units");

        $collection = parent::OrderBy($units, $properties, $orderBy);
        return $collection instanceof Units ? $collection : null;
    }
}