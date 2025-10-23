<?php

namespace API_ProfilingEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\TitleRelations;

class Title extends Entity
{
    private TitleRelations $relations;

    /**
     * Initializes a new instance of the Title class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_ProfilingRepositories_Model\Title $_entity The raw Title DTO.
     * @param TitleRelations $_relations The collection of all TitleRelations.
     */
    public function __construct(\API_ProfilingRepositories_Model\Title $_entity, TitleRelations $_relations)
    {
        // Call the parent constructor with just the core DTO.
        parent::__construct($_entity);
        $this->relations = $_relations->where(fn($n) => $n->TitleId == $_entity->Id);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_ProfilingRepositories_Model\Title
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Title) {
            throw new EntityException('invalid_entity_name', [':name' => \API_ProfilingRepositories_Model\Title::class]);
        }

        return $entity;
    }

    public function titleRelations(): TitleRelations
    {
        return $this->relations;
    }
}