<?php

namespace API_ProfilingEntities_Model;

use API_Assets\Classes\DTOException;
use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\CivilityRelations;

class Civility extends Entity
{
    private CivilityRelations $relations;

    /**
     * Initializes a new instance of the Civility class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_ProfilingRepositories_Model\Civility $_entity The raw Civility DTO.
     * @param CivilityRelations $_relations The collection of all CivilityRelations.
     */
    public function __construct(\API_ProfilingRepositories_Model\Civility $_entity, CivilityRelations $_relations)
    {
        // Call the parent constructor with just the core DTO.
        parent::__construct($_entity);
        $this->relations = $_relations->where(fn($n) => $n->CivilityId == $_entity->Id);
    }

    /**
     * @throws DTOException
     */
    public function it(): \API_ProfilingRepositories_Model\Civility
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Civility) {
            throw new DTOException('invalid_entity_name', [':name' => \API_ProfilingRepositories_Model\Civility::class]);
        }

        return $entity;
    }

    public function civilityRelations(): CivilityRelations
    {
        return $this->relations;
    }
}