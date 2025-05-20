<?php

namespace API_InventoryEntities_Model;

use API_DTOEntities_Model\Entity;
use API_ProfilingEntities_Model\Credential;
use API_RelationRepositories_Collection\InventoryRelations;
use UnexpectedValueException;

class Inventory extends Entity
{
    private Unit $unit;
    private Stock $stock;
    private ?DeliveryNote $deliveryNote;
    private ?DispatchNote $dispatchNote;
    private ?ReturnNote $returnNote;
    private ?WasteNote $wasteNote;
    private ?TransferNote $transferNote;
    private ?InventNote $inventNote;
    private ?Supplier $supplier;
    private ?Customer $customer;
    private ?Credential $credential;
    private InventoryRelations $relations;

    public function __construct(\API_InventoryRepositories_Model\Inventory $_entity, Unit $_unit, Stock $_stock, ?DeliveryNote $_deliveryNote, ?DispatchNote $_dispatchNote,
                                ?ReturnNote $_returnNote, ?WasteNote $_wasteNote, ?TransferNote $_transferNote, ?InventNote $_inventNote, ?Supplier $_supplier, ?Customer $_customer,
                                ?Credential $_credential, InventoryRelations $_relations)
    {
        parent::__construct($_entity, null);
        $this->unit = $_unit;
        $this->stock = $_stock;
        $this->deliveryNote = $_deliveryNote;
        $this->dispatchNote = $_dispatchNote;
        $this->returnNote = $_returnNote;
        $this->wasteNote = $_wasteNote;
        $this->transferNote = $_transferNote;
        $this->inventNote = $_inventNote;
        $this->supplier = $_supplier;
        $this->customer = $_customer;
        $this->credential  = $_credential;
        $this->relations = $_relations->Where(fn($n) => $n->InventId == $_entity->Id);
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

    public function Note(): DeliveryNote|DispatchNote|ReturnNote|WasteNote|TransferNote|InventNote
    {
        return $this->deliveryNote ?? $this->dispatchNote ?? $this->returnNote ?? $this->wasteNote ?? $this->inventNote;
    }

    public function Partner(): Supplier|Customer|Credential
    {
        return $this->supplier ?? $this->customer ?? $this->credential;
    }

    public function InventoryRelations(): InventoryRelations
    {
        return $this->relations;
    }
}