<?php

namespace API_InventoryEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;

class Stock extends Entity
{
    private Packaging $packaging;
    private Product $product;
    private Unit $unit;

    /**
     * Initializes a new instance of the Stock class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_InventoryRepositories_Model\Stock $_entity The raw Stock DTO.
     * @param Packaging $_packaging he related packaging.
     * @param Product $_product he related ProductC.
     * @param Unit $_unit he related Unit.
     */
    public function __construct(\API_InventoryRepositories_Model\Stock $_entity, Packaging $_packaging, Product $_product, Unit $_unit)
    {
        parent::__construct($_entity);
        $this->packaging = $_packaging;
        $this->product = $_product;
        $this->unit = $_unit;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_InventoryRepositories_Model\Stock
    {
        $entity = parent::it();
        if (!$entity instanceof \API_InventoryRepositories_Model\Stock) {
            throw new EntityException('invalid_entity_name', [':name' => \API_InventoryRepositories_Model\Stock::class]);
        }

        return $entity;
    }

    public function Packaging(): Packaging
    {
        return $this->packaging;
    }

    public function Product(): Product
    {
        return $this->product;
    }

    public function Unit(): Unit
    {
        return $this->unit;
    }
}