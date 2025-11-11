<?php

namespace API_InventoryEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;

class ProductCategory extends Entity
{
    /**
     * Initializes a new instance of the ProductCategory class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_InventoryRepositories_Model\ProductCategory $_entity The raw ProductCategory DTO.
     */
    public function __construct(\API_InventoryRepositories_Model\ProductCategory $_entity)
    {
        parent::__construct($_entity);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_InventoryRepositories_Model\ProductCategory
    {
        $entity = parent::it();
        if (!$entity instanceof \API_InventoryRepositories_Model\ProductCategory) {
            throw new EntityException('invalid_entity_name', [':name' => \API_InventoryRepositories_Model\ProductCategory::class]);
        }

        return $entity;
    }
}