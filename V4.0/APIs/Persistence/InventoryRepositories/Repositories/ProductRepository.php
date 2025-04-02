<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\Products;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\Product;
use Exception;
use TS_Utility\Enums\OrderEnum;

class ProductRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Product
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Product ? $entity : null;
    }

    public function GetAll(): ?Products
    {
        $collection = parent::GetAll();
        return $collection instanceof Products ? $collection : null;
    }

    public function GetById(string $id): ?Product
    {
        $entity = parent::GetById($id);
        return $entity instanceof Product ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Products
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Products ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Product
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Product ? $entity : null;
    }

    public function OrderBy(Collectable $products, array $properties, array $orderBy = [OrderEnum::ASC]): ?Products
    {
        if (!$products instanceof Products)
            throw new Exception("Products must be instance of Products");

        $collection = parent::OrderBy($products, $properties, $orderBy);
        return $collection instanceof Products ? $collection : null;
    }
}