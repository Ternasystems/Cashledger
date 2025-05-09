<?php

namespace API_InventoryEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\InventoryRelations;
use UnexpectedValueException;

class Inventory extends Entity
{
    private Unit $unit;
    private Stock $stock;
    private ?DeliveryNote $deliveryNote;
    private ?DispatchNote $dispatchNote;
    private ?Supplier $supplier;
    private ?Customer $customer;
    private InventoryRelations $relations;

    public function __construct(\API_InventoryRepositories_Model\Inventory $_entity, Unit $_unit, Stock $_stock, ?DeliveryNote $_deliveryNote, ?DispatchNote $_dispatchNote,
                                ?Supplier $_supplier, ?Customer $_customer, InventoryRelations $_relations)
    {
        parent::__construct($_entity, null);
        $this->unit = $_unit;
        $this->stock = $_stock;
        $this->deliveryNote = $_deliveryNote;
        $this->dispatchNote = $_dispatchNote;
        $this->supplier = $_supplier;
        $this->customer = $_customer;
        $this->relations = $_relations->Where(fn($n) => $n->InventoryId == $_entity->Id);
    }

    public function It(): \API_InventoryRepositories_Model\Inventory
    {
        $entity = parent::It();
        if (!$entity instanceof \API_InventoryRepositories_Model\Inventory)
            throw new UnexpectedValueException('Object must be an instance of '.\API_InventoryRepositories_Model\Inventory::class);

        return $entity;
    }

    public function Unit(): Unit
    {
        return $this->unit;
    }

    public function Stock(): Stock
    {
        return $this->stock;
    }

    public function Note(): DeliveryNote|DispatchNote
    {
        return $this->deliveryNote ?? $this->dispatchNote;
    }

    public function Partner(): Supplier|Customer
    {
        return $this->supplier ?? $this->customer;
    }

    public function InventoryRelations(): InventoryRelations
    {
        return $this->relations;
    }
}