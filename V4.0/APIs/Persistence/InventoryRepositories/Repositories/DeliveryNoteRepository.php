<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_InventoryRepositories_Collection\DeliveryNotes;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\DeliveryNote;
use Exception;
use TS_Utility\Enums\OrderEnum;

class DeliveryNoteRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?DeliveryNote
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof DeliveryNote ? $entity : null;
    }

    public function GetAll(): ?DeliveryNotes
    {
        $collection = parent::GetAll();
        return $collection instanceof DeliveryNotes ? $collection : null;
    }

    public function GetById(string $id): ?DeliveryNote
    {
        $entity = parent::GetById($id);
        return $entity instanceof DeliveryNote ? $entity : null;
    }

    public function GetBy(callable $predicate): ?DeliveryNotes
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof DeliveryNotes ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?DeliveryNote
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof DeliveryNote ? $entity : null;
    }

    public function OrderBy(Collectable $deliverynotes, array $properties, array $orderBy = [OrderEnum::ASC]): ?DeliveryNotes
    {
        if (!$deliverynotes instanceof DeliveryNotes)
            throw new Exception("DeliveryNotes must be instance of DeliveryNotes");

        $collection = parent::OrderBy($deliverynotes, $properties, $orderBy);
        return $collection instanceof DeliveryNotes ? $collection : null;
    }
}