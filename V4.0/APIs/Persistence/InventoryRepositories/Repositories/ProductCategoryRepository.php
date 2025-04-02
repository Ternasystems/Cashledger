<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\ProductCategories;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\ProductCategory;
use Exception;
use TS_Utility\Enums\OrderEnum;

class ProductCategoryRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ProductCategory
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof ProductCategory ? $entity : null;
    }

    public function GetAll(): ?ProductCategories
    {
        $collection = parent::GetAll();
        return $collection instanceof ProductCategories ? $collection : null;
    }

    public function GetById(string $id): ?ProductCategory
    {
        $entity = parent::GetById($id);
        return $entity instanceof ProductCategory ? $entity : null;
    }

    public function GetBy(callable $predicate): ?ProductCategories
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof ProductCategories ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ProductCategory
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof ProductCategory ? $entity : null;
    }

    public function OrderBy(Collectable $productCategories, array $properties, array $orderBy = [OrderEnum::ASC]): ?ProductCategories
    {
        if (!$productCategories instanceof ProductCategories)
            throw new Exception("ProductCategories must be instance of ProductCategories");

        $collection = parent::OrderBy($productCategories, $properties, $orderBy);
        return $collection instanceof ProductCategories ? $collection : null;
    }
}