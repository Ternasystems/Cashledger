<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\Stocks;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\Stock;
use Exception;
use TS_Utility\Enums\OrderEnum;

class StockRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Stock
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Stock ? $entity : null;
    }

    public function GetAll(): ?Stocks
    {
        $collection = parent::GetAll();
        return $collection instanceof Stocks ? $collection : null;
    }

    public function GetById(string $id): ?Stock
    {
        $entity = parent::GetById($id);
        return $entity instanceof Stock ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Stocks
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Stocks ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Stock
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Stock ? $entity : null;
    }

    public function OrderBy(Collectable $stocks, array $properties, array $orderBy = [OrderEnum::ASC]): ?Stocks
    {
        if (!$stocks instanceof Stocks)
            throw new Exception("Stocks must be instance of Stocks");

        $collection = parent::OrderBy($stocks, $properties, $orderBy);
        return $collection instanceof Stocks ? $collection : null;
    }
}