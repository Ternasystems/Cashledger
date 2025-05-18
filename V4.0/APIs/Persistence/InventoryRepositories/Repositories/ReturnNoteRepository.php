<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\ReturnNotes;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\ReturnNote;
use Exception;
use TS_Utility\Enums\OrderEnum;

class ReturnNoteRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?ReturnNote
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof ReturnNote ? $entity : null;
    }

    public function GetAll(): ?ReturnNotes
    {
        $collection = parent::GetAll();
        return $collection instanceof ReturnNotes ? $collection : null;
    }

    public function GetById(string $id): ?ReturnNote
    {
        $entity = parent::GetById($id);
        return $entity instanceof ReturnNote ? $entity : null;
    }

    public function GetBy(callable $predicate): ?ReturnNotes
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof ReturnNotes ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?ReturnNote
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof ReturnNote ? $entity : null;
    }

    public function OrderBy(Collectable $returnnotes, array $properties, array $orderBy = [OrderEnum::ASC]): ?ReturnNotes
    {
        if (!$returnnotes instanceof ReturnNotes)
            throw new Exception("ReturnNotes must be instance of ReturnNotes");

        $collection = parent::OrderBy($returnnotes, $properties, $orderBy);
        return $collection instanceof ReturnNotes ? $collection : null;
    }
}