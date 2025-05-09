<?php

namespace API_InventoryEntities_Model;

use API_DTOEntities_Model\Entity;
use API_InventoryEntities_Collection\StockAttributes;
use API_RelationRepositories_Collection\StockRelations;
use UnexpectedValueException;

class Stock extends Entity
{
    private Unit $unit;
    private Warehouse $warehouse;
    private Packaging $packaging;
    private Product $product;
    private ?StockAttributes $attributes;
    private ?StockRelations $relations;

    public function __construct(\API_InventoryRepositories_Model\Stock $_entity, Product $_product, Unit $_unit, Warehouse $_warehouse, Packaging $_packaging,
                                ?StockAttributes $_attributes, ?StockRelations $_relations)
    {
        parent::__construct($_entity, null);
        $this->unit = $_unit;
        $this->warehouse = $_warehouse;
        $this->packaging = $_packaging;
        $this->product = $_product;
        $this->attributes = $_attributes;
        $this->relations = $_relations?->Where(fn($n) => $n->StockId == $_entity->Id);
    }

    public function It(): \API_InventoryRepositories_Model\Stock
    {
        $entity = parent::It();
        if (!$entity instanceof \API_InventoryRepositories_Model\Stock)
            throw new UnexpectedValueException('Object must be an instance of '.\API_InventoryRepositories_Model\Stock::class);

        return $entity;
    }

    public function Unit(): Unit
    {
        return $this->unit;
    }

    public function Warehouse(): Warehouse
    {
        return $this->warehouse;
    }

    public function Packaging(): Packaging
    {
        return $this->packaging;
    }

    public function Product(): Product
    {
        return $this->product;
    }

    public function StockAttributes(): ?StockAttributes
    {
        return $this->attributes;
    }

    public function StockRelations(): ?StockRelations
    {
        return $this->relations;
    }
}