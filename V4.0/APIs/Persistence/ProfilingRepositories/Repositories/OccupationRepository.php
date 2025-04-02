<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Collection\Occupations;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Occupation;
use Exception;
use TS_Utility\Enums\OrderEnum;

class OccupationRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Occupation
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Occupation ? $entity : null;
    }

    public function GetAll(): ?Occupations
    {
        $collection = parent::GetAll();
        return $collection instanceof Occupations ? $collection : null;
    }

    public function GetById(string $id): ?Occupation
    {
        $entity = parent::GetById($id);
        return $entity instanceof Occupation ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Occupations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Occupations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Occupation
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Occupation ? $entity : null;
    }

    /**
     * @throws Exception
     */
    public function OrderBy(Collectable $occupations, array $properties, array $orderBy = [OrderEnum::ASC]): ?Occupations
    {
        if (!$occupations instanceof Occupations)
            throw new Exception("Occupations must be instance of Occupations");

        $collection = parent::OrderBy($occupations, $properties, $orderBy);
        return $collection instanceof Occupations ? $collection : null;
    }
}