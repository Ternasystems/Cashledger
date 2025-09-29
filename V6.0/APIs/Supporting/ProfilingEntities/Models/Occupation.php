<?php

namespace API_ProfilingEntities_Model;

use API_Assets\DTOException;
use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\OccupationRelations;

class Occupation extends Entity
{
    private OccupationRelations $relations;

    /**
     * Initializes a new instance of the Occupation class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_ProfilingRepositories_Model\Occupation $_entity The raw Occupation DTO.
     * @param OccupationRelations $_relations The collection of all OccupationRelations.
     */
    public function __construct(\API_ProfilingRepositories_Model\Occupation $_entity, OccupationRelations $_relations)
    {
        // Call the parent constructor with just the core DTO.
        parent::__construct($_entity);
        $this->relations = $_relations->where(fn($n) => $n->AppId == $_entity->Id);
    }

    /**
     * @throws DTOException
     */
    public function it(): \API_ProfilingRepositories_Model\Occupation
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Occupation) {
            throw new DTOException('invalid_entity_name');
        }

        return $entity;
    }

    public function occupationRelations(): OccupationRelations
    {
        return $this->relations;
    }
}