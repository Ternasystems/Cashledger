<?php

namespace API_ProfilingEntities_Model;

use API_Assets\DTOException;
use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\StatusRelations;

class Status extends Entity
{
    private StatusRelations $relations;

    /**
     * Initializes a new instance of the Status class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_ProfilingRepositories_Model\Status $_entity The raw Status DTO.
     * @param StatusRelations $_relations The collection of all StatusRelations.
     */
    public function __construct(\API_ProfilingRepositories_Model\Status $_entity, StatusRelations $_relations)
    {
        // Call the parent constructor with just the core DTO.
        parent::__construct($_entity);
        $this->relations = $_relations->where(fn($n) => $n->AppId == $_entity->Id);
    }

    /**
     * @throws DTOException
     */
    public function it(): \API_ProfilingRepositories_Model\Status
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Status) {
            throw new DTOException('invalid_entity_name');
        }

        return $entity;
    }

    public function statusRelations(): StatusRelations
    {
        return $this->relations;
    }
}