<?php

namespace API_InventoryEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;

class Product extends Entity
{
    private ProductCategory $category;

    /**
     * Initializes a new instance of the Product class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_InventoryRepositories_Model\Product $_entity The raw Product DTO.
     * @param ProductCategory $_category The related ProductCategory.
     */
    public function __construct(\API_InventoryRepositories_Model\Product $_entity, ProductCategory $_category)
    {
        parent::__construct($_entity);
        $this->category = $_category;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_InventoryRepositories_Model\Product
    {
        $entity = parent::it();
        if (!$entity instanceof \API_InventoryRepositories_Model\Product) {
            throw new EntityException('invalid_entity_name', [':name' => \API_InventoryRepositories_Model\Product::class]);
        }

        return $entity;
    }

    public function ProductCategory(): ProductCategory
    {
        return $this->category;
    }
}