<?php

namespace API_BillingEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\PriceRelations;

class Price extends Entity
{
    private PriceRelations $relations;

    /**
     * Initializes a new instance of the App class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_BillingRepositories_model\Price $_entity The raw App DTO.
     * @param PriceRelations $_relations The collection of all AppRelations.
     */
    public function __construct(\API_BillingRepositories_model\Price $_entity, PriceRelations $_relations)
    {
        // Call the parent constructor with just the core DTO.
        parent::__construct($_entity);
        $this->relations = $_relations->where(fn($n) => $n->PriceId == $_entity->Id);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_BillingRepositories_model\Price
    {
        $entity = parent::it();
        if (!$entity instanceof \API_BillingRepositories_model\Price) {
            throw new EntityException('invalid_entity_name', [':name' => \API_BillingRepositories_model\Price::class]);
        }

        return $entity;
    }

    public function priceRelations(): PriceRelations
    {
        return $this->relations;
    }
}