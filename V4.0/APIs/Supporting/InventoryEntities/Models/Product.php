<?php

namespace API_InventoryEntities_Model;

use API_DTOEntities_Model\Entity;
use API_InventoryEntities_Collection\ProductAttributes;
use UnexpectedValueException;

class Product extends Entity
{
    private ProductCategory $category;
    private ?ProductAttributes $attributes;

    public function __construct(\API_InventoryRepositories_Model\Product $_entity, ProductCategory $_category, ?ProductAttributes $_attributes)
    {
        parent::__construct($_entity, null);
        $this->category = $_category;
        $this->attributes = $_attributes;
    }

    public function It(): \API_InventoryRepositories_Model\Product
    {
        $entity = parent::It();
        if (!$entity instanceof \API_InventoryRepositories_Model\Product)
            throw new UnexpectedValueException('Object must be an instance of '.\API_InventoryRepositories_Model\Product::class);

        return $entity;
    }

    public function ProductCategory(): ProductCategory
    {
        return $this->category;
    }

    public function ProductAttributes(): ?ProductAttributes
    {
        return $this->attributes;
    }
}