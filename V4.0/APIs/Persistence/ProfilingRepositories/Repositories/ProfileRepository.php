<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Collection\Profiles;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Profile;
use Exception;
use TS_Utility\Enums\OrderEnum;

class ProfileRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Profile
    {
        $entity = parent::FirstOrDefault($predicate);
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

    public function GetBy(callable $predicate): ?Profiles
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Profiles ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Profile
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Profile ? $entity : null;
    }

    /**
     * @throws Exception
     */
    public function OrderBy(Collectable $profiles, array $properties, array $orderBy = [OrderEnum::ASC]): ?Profiles
    {
        if (!$profiles instanceof Profiles)
            throw new Exception("Profiles must be instance of Profiles");

        $collection = parent::OrderBy($profiles, $properties, $orderBy);
        return $collection instanceof Profiles ? $collection : null;
    }
}