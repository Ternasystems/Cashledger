<?php

namespace API_TaxesEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;

class Tax extends Entity
{
    /**
     * Initializes a new instance of the AppCategory class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_TaxesRepositories_Model\Tax $_entity The raw AppCategory DTO.
     */
    public function __construct(\API_TaxesRepositories_Model\Tax $_entity)
    {
        parent::__construct($_entity);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_TaxesRepositories_Model\Tax
    {
        $entity = parent::it();
        if (!$entity instanceof \API_TaxesRepositories_Model\Tax) {
            throw new EntityException('invalid_entity_name', [':name' => \API_TaxesRepositories_Model\Tax::class]);
        }

        return $entity;
    }
}