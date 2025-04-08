<?php

namespace API_InventoryEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\AttributeRelations;
use API_RelationRepositories_Collection\LanguageRelations;
use UnexpectedValueException;

class ProductAttribute extends Entity
{
    private ?AttributeRelations $relations;

    public function __construct(\API_InventoryRepositories_Model\ProductAttribute $_entity, ?AttributeRelations $_relations, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
        $this->relations = $_relations?->Where(fn($n) => $n->AttributeId == $_entity->Id);
    }

    public function It(): \API_InventoryRepositories_Model\ProductAttribute
    {
        $entity = parent::It();
        if (!$entity instanceof \API_InventoryRepositories_Model\ProductAttribute)
            throw new UnexpectedValueException('Object must be an instance of '.\API_InventoryRepositories_Model\ProductAttribute::class);

        return $entity;
    }

    public function AttributeRelations(): ?AttributeRelations
    {
        return $this->relations;
    }
}