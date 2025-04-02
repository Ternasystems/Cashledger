<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Collection\Apps;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\App;
use TS_Utility\Enums\OrderEnum;

class AppRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?App
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof App ? $entity : null;
    }

    public function GetAll(): ?Apps
    {
        $collection = parent::GetAll();
        return $collection instanceof Apps ? $collection : null;
    }

    public function GetById(string $id): ?App
    {
        $entity = parent::GetById($id);
        return $entity instanceof App ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Apps
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Apps ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?App
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof App ? $entity : null;
    }

    public function OrderBy(Collectable $apps, array $properties, array $orderBy = [OrderEnum::ASC]): ?Apps
    {
        if (!$apps instanceof Apps)
            throw new Exception("Apps must be instance of Apps");

        $collection = parent::OrderBy($apps, $properties, $orderBy);
        return $collection instanceof Apps ? $collection : null;
    }
}