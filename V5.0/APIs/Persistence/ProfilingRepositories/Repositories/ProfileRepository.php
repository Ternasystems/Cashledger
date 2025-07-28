<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Profiles;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Profile;
use Closure;

class ProfileRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Profile
    {
        $entity = parent::first($predicate);
        return $entity instanceof Profile ? $entity : null;
    }

    public function GetAll(): ?Profiles
    {
        $collection = parent::GetAll();
        return $collection instanceof Profiles ? $collection : null;
    }

    public function GetById(string $id): ?Profile
    {
        $entity = parent::GetById($id);
        return $entity instanceof Profile ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?Profiles
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Profiles ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Profile
    {
        $entity = parent::last($predicate);
        return $entity instanceof Profile ? $entity : null;
    }
}