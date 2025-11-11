<?php

namespace API_BillingEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;

class Currency extends Entity
{
    /**
     * Initializes a new instance of the AppCategory class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_BillingRepositories_Model\Currency $_entity The raw AppCategory DTO.
     */
    public function __construct(\API_BillingRepositories_Model\Currency $_entity)
    {
        parent::__construct($_entity);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_BillingRepositories_Model\Currency
    {
        $entity = parent::it();
        if (!$entity instanceof \API_BillingRepositories_Model\Currency) {
            throw new EntityException('invalid_entity_name', [':name' => \API_BillingRepositories_Model\Currency::class]);
        }

        return $entity;
    }
}