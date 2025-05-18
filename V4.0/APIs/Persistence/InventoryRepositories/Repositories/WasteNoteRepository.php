<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\WasteNotes;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\WasteNote;
use Exception;
use TS_Utility\Enums\OrderEnum;

class WasteNoteRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?WasteNote
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof WasteNote ? $entity : null;
    }

    public function GetAll(): ?WasteNotes
    {
        $collection = parent::GetAll();
        return $collection instanceof WasteNotes ? $collection : null;
    }

    public function GetById(string $id): ?WasteNote
    {
        $entity = parent::GetById($id);
        return $entity instanceof WasteNote ? $entity : null;
    }

    public function GetBy(callable $predicate): ?WasteNotes
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof WasteNotes ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?WasteNote
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof WasteNote ? $entity : null;
    }

    public function OrderBy(Collectable $wastenotes, array $properties, array $orderBy = [OrderEnum::ASC]): ?WasteNotes
    {
        if (!$wastenotes instanceof WasteNotes)
            throw new Exception("WasteNotes must be instance of WasteNotes");

        $collection = parent::OrderBy($wastenotes, $properties, $orderBy);
        return $collection instanceof WasteNotes ? $collection : null;
    }
}