<?php

namespace API_ProfilingEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\CivilityRelations;
use API_RelationRepositories_Collection\LanguageRelations;
use UnexpectedValueException;

class Civility extends Entity
{
    private CivilityRelations $relations;

    public function __construct(\API_ProfilingRepositories_Model\Civility $_entity, CivilityRelations $_relations, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
        $this->relations = $_relations->Where(fn($n) => $n->CivilityId == $_entity->Id);
    }

    public function It(): \API_ProfilingRepositories_Model\Civility
    {
        $entity = parent::It();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Civility)
            throw new UnexpectedValueException('Object must be an instance of '.\API_ProfilingRepositories_Model\Civility::class);

        return $entity;
    }

    public function CivilityRelations(): CivilityRelations
    {
        return $this->relations;
    }
}