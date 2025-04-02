<?php

namespace API_ProfilingEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\LanguageRelations;
use API_RelationRepositories_Collection\TitleRelations;
use http\Exception\UnexpectedValueException;

class Title extends Entity
{
    private TitleRelations $relations;

    public function __construct(\API_ProfilingRepositories_Model\Title $_entity, TitleRelations $_relations, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
        $this->relations = $_relations->Where(fn($n) => $n->TitleId == $_entity->Id);
    }

    public function It(): \API_ProfilingRepositories_Model\Title
    {
        $entity = parent::It();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Title)
            throw new UnexpectedValueException('Object must be an instance of '.\API_ProfilingRepositories_Model\Title::class);

        return $entity;
    }

    public function TitleRelations(): TitleRelations
    {
        return $this->relations;
    }
}