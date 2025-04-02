<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\ProductAttributes;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\ProductAttribute;
use Exception;
use TS_Utility\Enums\OrderEnum;

class ProductAttributeRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ProductAttribute
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof ProductAttribute ? $entity : null;
    }

    public function GetAll(): ?ProductAttributes
    {
        $collection = parent::GetAll();
        return $collection instanceof ProductAttributes ? $collection : null;
    }

    public function GetById(string $id): ?ProductAttribute
    {
        $entity = parent::GetById($id);
        return $entity instanceof ProductAttribute ? $entity : null;
    }

    public function GetBy(callable $predicate): ?ProductAttributes
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof ProductAttributes ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ProductAttribute
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof ProductAttribute ? $entity : null;
    }

    public function OrderBy(Collectable $productAttributes, array $properties, array $orderBy = [OrderEnum::ASC]): ?ProductAttributes
    {
        if (!$productAttributes instanceof ProductAttributes)
            throw new Exception("ProductAttributes must be instance of ProductAttributes");

        $collection = parent::OrderBy($productAttributes, $properties, $orderBy);
        return $collection instanceof ProductAttributes ? $collection : null;
    }
}