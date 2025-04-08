<?php

namespace API_InventoryEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\InventoryRelations;
use UnexpectedValueException;

class Inventory extends Entity
{
    private Unit $unit;
    private Stock $stock;
    private Customer|Supplier $partner;
    private InventoryRelations $relations;

    public function __construct(\API_InventoryRepositories_Model\Inventory $_entity, Unit $_unit, Stock $_stock, Customer|Supplier $_partner, InventoryRelations $_relations)
    {
        parent::__construct($_entity, null);
        $this->unit = $_unit;
        $this->stock = $_stock;
        $this->partner = $_partner;
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

    public function Partner(): Customer|Supplier
    {
        return $this->partner;
    }

    public function InventoryRelations(): InventoryRelations
    {
        return $this->relations;
    }
}