<?php

namespace API_DTOEntities_Model;

use API_Assets\Classes\DTOException;
use API_RelationRepositories_Collection\AppRelations;

class App extends Entity
{
    private AppRelations $relations;

    /**
     * Initializes a new instance of the App class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_DTORepositories_Model\App $_entity The raw App DTO.
     * @param AppRelations $_relations The collection of all AppRelations.
     */
    public function __construct(\API_DTORepositories_Model\App $_entity, AppRelations $_relations)
    {
        // Call the parent constructor with just the core DTO.
        parent::__construct($_entity);
        $this->relations = $_relations->where(fn($n) => $n->AppId == $_entity->Id);
    }

    /**
     * @throws DTOException
     */
    public function it(): \API_DTORepositories_Model\App
    {
        $entity = parent::it();
        if (!$entity instanceof \API_DTORepositories_Model\App) {
            throw new DTOException('invalid_entity_name', [':name' => \API_DTORepositories_Model\App::class]);
        }

        return $entity;
    }

    public function appRelations(): AppRelations
    {
        return $this->relations;
    }
}