<?php

namespace API_DTOEntities_Model;

use API_Assets\DTOException;
use API_RelationRepositories_Collection\ParameterRelations;
use API_RelationRepositories_Model\ParameterRelation;

class Parameter extends Entity
{
    private ParameterRelation $relations;

    /**
     * Initializes a new instance of the Parameter class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_DTORepositories_Model\Parameter $_entity The raw Parameter DTO.
     * @param ParameterRelations $_relations The collection of all ParameterRelations.
     */
    public function __construct(\API_DTORepositories_Model\Parameter $_entity, ParameterRelations $_relations)
    {
        // Call the parent constructor with just the core DTO.
        parent::__construct($_entity);
        $this->relations = $_relations->where(fn($n) => $n->ParameterId == $_entity->Id);
    }

    /**
     * @throws DTOException
     */
    public function it(): \API_DTORepositories_Model\Parameter
    {
        $entity = parent::it();
        if (!$entity instanceof \API_DTORepositories_Model\Parameter) {
            throw new DTOException('invalid_entity_name');
        }

        return $entity;
    }

    public function parameterRelations(): ParameterRelations
    {
        return $this->relations;
    }
}