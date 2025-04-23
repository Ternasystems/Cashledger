<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\DispatchNotes;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\DispatchNote;
use Exception;
use TS_Utility\Enums\OrderEnum;

class DispatchNoteRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?DispatchNote
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof DispatchNote ? $entity : null;
    }

    public function GetAll(): ?DispatchNotes
    {
        $collection = parent::GetAll();
        return $collection instanceof DispatchNotes ? $collection : null;
    }

    public function GetById(string $id): ?DispatchNote
    {
        $entity = parent::GetById($id);
        return $entity instanceof DispatchNote ? $entity : null;
    }

    public function GetBy(callable $predicate): ?DispatchNotes
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof DispatchNotes ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?DispatchNote
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof DispatchNote ? $entity : null;
    }

    public function OrderBy(Collectable $disptachnotes, array $properties, array $orderBy = [OrderEnum::ASC]): ?DispatchNotes
    {
        if (!$disptachnotes instanceof DispatchNotes)
            throw new Exception("DispatchNotes must be instance of DispatchNotes");

        $collection = parent::OrderBy($disptachnotes, $properties, $orderBy);
        return $collection instanceof DispatchNotes ? $collection : null;
    }
}