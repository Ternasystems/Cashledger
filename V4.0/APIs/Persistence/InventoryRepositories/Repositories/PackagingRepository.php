<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\Packagings;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\Packaging;
use Exception;
use TS_Utility\Enums\OrderEnum;

class PackagingRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Packaging
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Packaging ? $entity : null;
    }

    public function GetAll(): ?Packagings
    {
        $collection = parent::GetAll();
        return $collection instanceof Packagings ? $collection : null;
    }

    public function GetById(string $id): ?Packaging
    {
        $entity = parent::GetById($id);
        return $entity instanceof Packaging ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Packagings
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Packagings ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Packaging
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Packaging ? $entity : null;
    }

    public function OrderBy(Collectable $packagings, array $properties, array $orderBy = [OrderEnum::ASC]): ?Packagings
    {
        if (!$packagings instanceof Packagings)
            throw new Exception("Packagings must be instance of Packagings");

        $collection = parent::OrderBy($packagings, $properties, $orderBy);
        return $collection instanceof Packagings ? $collection : null;
    }
}