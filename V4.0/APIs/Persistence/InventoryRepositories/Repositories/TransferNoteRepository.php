<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\TransferNotes;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\TransferNote;
use TS_Utility\Enums\OrderEnum;

class TransferNoteRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?TransferNote
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof TransferNote ? $entity : null;
    }

    public function GetAll(): ?TransferNotes
    {
        $collection = parent::GetAll();
        return $collection instanceof TransferNotes ? $collection : null;
    }

    public function GetById(string $id): ?TransferNote
    {
        $entity = parent::GetById($id);
        return $entity instanceof TransferNote ? $entity : null;
    }

    public function GetBy(callable $predicate): ?TransferNotes
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof TransferNotes ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?TransferNote
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof TransferNote ? $entity : null;
    }

    public function OrderBy(Collectable $transfernotes, array $properties, array $orderBy = [OrderEnum::ASC]): ?TransferNotes
    {
        if (!$transfernotes instanceof TransferNotes)
            throw new Exception("TransferNotes must be instance of TransferNotes");

        $collection = parent::OrderBy($transfernotes, $properties, $orderBy);
        return $collection instanceof TransferNotes ? $collection : null;
    }
}