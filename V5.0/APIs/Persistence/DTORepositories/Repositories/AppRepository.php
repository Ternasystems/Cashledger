<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Apps;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\App;
use Closure;

class AppRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?App
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?Apps
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Apps ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?App
    {
        $entity = parent::last($predicate);
        return $entity instanceof App ? $entity : null;
    }
}