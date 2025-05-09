<?php

namespace API_InventoryEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\LanguageRelations;
use API_RelationRepositories_Collection\StockRelations;
use UnexpectedValueException;

class StockAttribute extends Entity
{
    private ?StockRelations $relations;

    public function __construct(\API_InventoryRepositories_Model\ProductAttribute $_entity, ?StockRelations $_relations, ?LanguageRelations $_languageRelations)
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

    public function StockRelations(): ?StockRelations
    {
        return $this->relations;
    }
}