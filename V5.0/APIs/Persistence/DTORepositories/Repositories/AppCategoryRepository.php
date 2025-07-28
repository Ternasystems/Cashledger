<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\AppCategories;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\AppCategory;
use Closure;

class AppCategoryRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?AppCategory
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?AppCategories
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof AppCategories ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?AppCategory
    {
        $entity = parent::last($predicate);
        return $entity instanceof AppCategory ? $entity : null;
    }
}