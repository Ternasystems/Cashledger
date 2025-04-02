<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Collection\Titles;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Title;
use Exception;
use TS_Utility\Enums\OrderEnum;

class TitleRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Title
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Title ? $entity : null;
    }

    public function GetAll(): ?Titles
    {
        $collection = parent::GetAll();
        return $collection instanceof Titles ? $collection : null;
    }

    public function GetById(string $id): ?Title
    {
        $entity = parent::GetById($id);
        return $entity instanceof Title ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Titles
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Titles ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Title
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Title ? $entity : null;
    }

    /**
     * @throws Exception
     */
    public function OrderBy(Collectable $titles, array $properties, array $orderBy = [OrderEnum::ASC]): ?Titles
    {
        if (!$titles instanceof Titles)
            throw new Exception("Titles must be instance of Titles");

        $collection = parent::OrderBy($titles, $properties, $orderBy);
        return $collection instanceof Titles ? $collection : null;
    }
}