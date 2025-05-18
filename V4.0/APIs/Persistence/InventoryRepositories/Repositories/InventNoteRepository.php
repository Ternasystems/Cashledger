<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\InventNotes;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\InventNote;
use Exception;
use TS_Utility\Enums\OrderEnum;

class InventNoteRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?InventNote
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof InventNote ? $entity : null;
    }

    public function GetAll(): ?InventNotes
    {
        $collection = parent::GetAll();
        return $collection instanceof InventNotes ? $collection : null;
    }

    public function GetById(string $id): ?InventNote
    {
        $entity = parent::GetById($id);
        return $entity instanceof InventNote ? $entity : null;
    }

    public function GetBy(callable $predicate): ?InventNotes
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof InventNotes ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?InventNote
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof InventNote ? $entity : null;
    }

    public function OrderBy(Collectable $inventnotes, array $properties, array $orderBy = [OrderEnum::ASC]): ?InventNotes
    {
        if (!$inventnotes instanceof InventNotes)
            throw new Exception("InventNotes must be instance of InventNotes");

        $collection = parent::OrderBy($inventnotes, $properties, $orderBy);
        return $collection instanceof InventNotes ? $collection : null;
    }
}