<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Genders;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Gender;
use Closure;

class GenderRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Gender
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?Genders
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Genders ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Gender
    {
        $entity = parent::last($predicate);
        return $entity instanceof Gender ? $entity : null;
    }
}