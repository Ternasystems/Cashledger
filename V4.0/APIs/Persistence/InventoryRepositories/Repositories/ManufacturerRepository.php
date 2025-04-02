<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\Manufacturers;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\Manufacturer;
use Exception;
use TS_Utility\Enums\OrderEnum;

class ManufacturerRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Manufacturer
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Manufacturer ? $entity : null;
    }

    public function GetAll(): ?Manufacturers
    {
        $collection = parent::GetAll();
        return $collection instanceof Manufacturers ? $collection : null;
    }

    public function GetById(string $id): ?Manufacturer
    {
        $entity = parent::GetById($id);
        return $entity instanceof Manufacturer ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Manufacturers
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Manufacturers ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Manufacturer
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Manufacturer ? $entity : null;
    }

    public function OrderBy(Collectable $manufacturers, array $properties, array $orderBy = [OrderEnum::ASC]): ?Manufacturers
    {
        if (!$manufacturers instanceof Manufacturers)
            throw new Exception("Manufacturers must be instance of Manufacturers");

        $collection = parent::OrderBy($manufacturers, $properties, $orderBy);
        return $collection instanceof Manufacturers ? $collection : null;
    }
}