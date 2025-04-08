<?php

namespace API_DTOEntities_Collection;

use API_DTOEntities_Model\AppCategory;

class AppCategories extends EntityCollectable
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