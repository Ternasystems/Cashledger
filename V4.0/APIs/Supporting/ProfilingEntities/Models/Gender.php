<?php

namespace API_ProfilingEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\GenderRelations;
use API_RelationRepositories_Collection\LanguageRelations;
use UnexpectedValueException;

class Gender extends Entity
{
    private GenderRelations $relations;

    public function __construct(\API_ProfilingRepositories_Model\Gender $_entity, GenderRelations $_relations, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
        $this->relations = $_relations->Where(fn($n) => $n->GenderId == $_entity->Id);
    }

    public function It(): \API_ProfilingRepositories_Model\Gender
    {
        $entity = parent::It();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Gender)
            throw new UnexpectedValueException('Object must be an instance of '.\API_ProfilingRepositories_Model\Gender::class);

        return $entity;
    }

    public function GenderRelations(): GenderRelations
    {
        return $this->relations;
    }
}