<?php

namespace API_ProfilingEntities_Model;

use API_Assets\Classes\DTOException;
use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\GenderRelations;

class Gender extends Entity
{
    private GenderRelations $relations;

    /**
     * Initializes a new instance of the Gender class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_ProfilingRepositories_Model\Gender $_entity The raw Gender DTO.
     * @param GenderRelations $_relations The collection of all GenderRelations.
     */
    public function __construct(\API_ProfilingRepositories_Model\Gender $_entity, GenderRelations $_relations)
    {
        // Call the parent constructor with just the core DTO.
        parent::__construct($_entity);
        $this->relations = $_relations->where(fn($n) => $n->GenderId == $_entity->Id);
    }

    /**
     * @throws DTOException
     */
    public function it(): \API_ProfilingRepositories_Model\Gender
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Gender) {
            throw new DTOException('invalid_entity_name', [':name' => \API_ProfilingRepositories_Model\Gender::class]);
        }

        return $entity;
    }

    public function genderRelations(): GenderRelations
    {
        return $this->relations;
    }
}