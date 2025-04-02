<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Collection\Countries;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Country;
use Exception;
use TS_Utility\Enums\OrderEnum;

class CountryRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Country
    {
        $entity = parent::FirstOrDefault($predicate);
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

    public function GetBy(callable $predicate): ?Countries
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Countries ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Country
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Country ? $entity : null;
    }

    public function OrderBy(Collectable $countries, array $properties, array $orderBy = [OrderEnum::ASC]): ?Countries
    {
        if (!$countries instanceof Countries)
            throw new Exception("Countries must be instance of Countries");

        $collection = parent::OrderBy($countries, $properties, $orderBy);
        return $collection instanceof Countries ? $collection : null;
    }
}