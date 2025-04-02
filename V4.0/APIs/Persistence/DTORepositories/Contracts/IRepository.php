<?php

namespace API_DTORepositories_Contract;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Model\DTOBase;
use TS_Utility\Enums\OrderEnum;

interface IRepository
{
    public function FirstOrDefault(callable $predicate): ?DTOBase;
    public function GetAll(): ?Collectable;
    public function GetById(string $id): ?DTOBase;
    public function GetBy(callable $predicate): ?Collectable;
    public function LastOrDefault(callable $predicate): ?DTOBase;
    public function Add(string $entityName = DTOBase::class, ?array $args = null): void;
    public function Remove(string $entityName = DTOBase::class, ?array $args = null): void;
    public function Deactivate(string $entityName = DTOBase::class, ?array $args = null): void;
    public function Update(string $entityName = DTOBase::class, ?array $args = null): void;
    public function OrderBy(Collectable $collection, array $properties, array $orderBy = [OrderEnum::ASC]): ?Collectable;
}