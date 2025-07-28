<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Civilities;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Civility;
use Closure;

class CivilityRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Civility
    {
        $entity = parent::first($predicate);
        return $entity instanceof Civility ? $entity : null;
    }

    public function GetAll(): ?Civilities
    {
        $collection = parent::GetAll();
        return $collection instanceof Civilities ? $collection : null;
    }

    public function GetById(string $id): ?Civility
    {
        $entity = parent::GetById($id);
        return $entity instanceof Civility ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?Civilities
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Civilities ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Civility
    {
        $entity = parent::last($predicate);
        return $entity instanceof Civility ? $entity : null;
    }
}