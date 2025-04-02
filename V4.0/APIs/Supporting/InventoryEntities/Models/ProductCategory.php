<?php

namespace API_InventoryEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\LanguageRelations;
use UnexpectedValueException;

class ProductCategory extends Entity
{
    public function __construct(\API_InventoryRepositories_Model\ProductCategory $_entity, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
    }

    public function It(): \API_InventoryRepositories_Model\ProductCategory
    {
        $entity = parent::It();
        if (!$entity instanceof \API_InventoryRepositories_Model\ProductCategory)
            throw new UnexpectedValueException('Object must be an instance of '.\API_InventoryRepositories_Model\ProductCategory::class);

        return $entity;
    }
}