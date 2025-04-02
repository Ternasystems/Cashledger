<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Collection\Genders;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Gender;
use Exception;
use TS_Utility\Enums\OrderEnum;

class GenderRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Gender
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Gender ? $entity : null;
    }

    public function GetAll(): ?Genders
    {
        $collection = parent::GetAll();
        return $collection instanceof Genders ? $collection : null;
    }

    public function GetById(string $id): ?Gender
    {
        $entity = parent::GetById($id);
        return $entity instanceof Gender ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Genders
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Genders ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Gender
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Gender ? $entity : null;
    }

    /**
     * @throws Exception
     */
    public function OrderBy(Collectable $genders, array $properties, array $orderBy = [OrderEnum::ASC]): ?Genders
    {
        if (!$genders instanceof Genders)
            throw new Exception("Genders must be instance of Genders");

        $collection = parent::OrderBy($genders, $properties, $orderBy);
        return $collection instanceof Genders ? $collection : null;
    }
}