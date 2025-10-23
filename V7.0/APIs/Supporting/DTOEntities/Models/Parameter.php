<?php

namespace API_DTOEntities_Model;

use API_Assets\Classes\EntityException;
use API_RelationRepositories_Collection\ParameterRelations;

class Parameter extends Entity
{
    private ParameterRelations $relations;

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
     * @throws EntityException
     */
    public function it(): \API_DTORepositories_Model\Parameter
    {
        $entity = parent::it();
        if (!$entity instanceof \API_DTORepositories_Model\Parameter) {
            throw new EntityException('invalid_entity_name', [':name' => \API_DTORepositories_Model\Parameter::class]);
        }

        return $entity;
    }

    public function parameterRelations(): ParameterRelations
    {
        return $this->relations;
    }
}