<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Collection\AppCategories;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\AppCategory;
use TS_Utility\Enums\OrderEnum;

class AppCategoryRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?AppCategory
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof AppCategory ? $entity : null;
    }

    public function GetAll(): ?AppCategories
    {
        $collection = parent::GetAll();
        return $collection instanceof AppCategories ? $collection : null;
    }

    public function GetById(string $id): ?AppCategory
    {
        $entity = parent::GetById($id);
        return $entity instanceof AppCategory ? $entity : null;
    }

    public function GetBy(callable $predicate): ?AppCategories
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof AppCategories ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?AppCategory
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof AppCategory ? $entity : null;
    }

    public function OrderBy(Collectable $appCategories, array $properties, array $orderBy = [OrderEnum::ASC]): ?AppCategories
    {
        if (!$appCategories instanceof AppCategories)
            throw new Exception("AppCategories must be instance of AppCategories");

        $collection = parent::OrderBy($appCategories, $properties, $orderBy);
        return $collection instanceof AppCategories ? $collection : null;
    }
}