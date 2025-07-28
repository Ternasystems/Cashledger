<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Countries;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Country;
use Closure;

class CountryRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Country
    {
        $entity = parent::first($predicate);
        return $entity instanceof Country ? $entity : null;
    }

    public function GetAll(): ?Countries
    {
        $collection = parent::GetAll();
        return $collection instanceof Countries ? $collection : null;
    }

    public function GetById(string $id): ?Country
    {
        $entity = parent::GetById($id);
        return $entity instanceof Country ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?Countries
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Countries ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Country
    {
        $entity = parent::last($predicate);
        return $entity instanceof Country ? $entity : null;
    }
}