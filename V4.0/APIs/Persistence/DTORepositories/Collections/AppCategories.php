<?php

namespace API_DTORepositories_Collection;

use API_DTORepositories_Model\AppCategory;

class AppCategories extends Collectable
{
    public function __construct(array $collection, string $objectType = AppCategory::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?AppCategory
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof AppCategory ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?AppCategory
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof AppCategory ? $entity : null;
    }
}