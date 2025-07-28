<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Titles;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Title;
use CLosure;

class TitleRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Title
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?Titles
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Titles ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Title
    {
        $entity = parent::last($predicate);
        return $entity instanceof Title ? $entity : null;
    }
}