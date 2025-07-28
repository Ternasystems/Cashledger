<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Occupations;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Occupation;
use Closure;

class OccupationRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Occupation
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?Occupations
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Occupations ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Occupation
    {
        $entity = parent::last($predicate);
        return $entity instanceof Occupation ? $entity : null;
    }
}