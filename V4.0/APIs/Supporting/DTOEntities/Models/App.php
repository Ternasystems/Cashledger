<?php

namespace API_DTOEntities_Model;

use API_RelationRepositories_Collection\AppRelations;
use API_RelationRepositories_Collection\LanguageRelations;
use UnexpectedValueException;

class App extends Entity
{
    private AppRelations $relations;

    public function __construct(\API_DTORepositories_Model\App $_entity, AppRelations $_relations, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
        $this->relations = $_relations->Where(fn($n) => $n->AppId == $_entity->Id);
    }

    public function It(): \API_DTORepositories_Model\App
    {
        $entity = parent::It();
        if (!$entity instanceof \API_DTORepositories_Model\App)
            throw new UnexpectedValueException('Object must be an instance of '.\API_DTORepositories_Model\App::class);

        return $entity;
    }

    public function AppRelations(): AppRelations
    {
        return $this->relations;
    }
}