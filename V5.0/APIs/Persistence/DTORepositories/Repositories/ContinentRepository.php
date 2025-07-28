<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Continents;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Continent;
use Closure;

class ContinentRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Continent
    {
        $entity = parent::first($predicate);
        return $entity instanceof Continent ? $entity : null;
    }

    public function GetAll(): ?Continents
    {
        $collection = parent::GetAll();
        return $collection instanceof Continents ? $collection : null;
    }

    public function GetById(string $id): ?Continent
    {
        $entity = parent::GetById($id);
        return $entity instanceof Continent ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?Continents
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Continents ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Continent
    {
        $entity = parent::last($predicate);
        return $entity instanceof Continent ? $entity : null;
    }
}