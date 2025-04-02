<?php

namespace API_ProfilingEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\LanguageRelations;
use API_RelationRepositories_Collection\OccupationRelations;
use http\Exception\UnexpectedValueException;

class Occupation extends Entity
{
    private OccupationRelations $relations;

    public function __construct(\API_ProfilingRepositories_Model\Occupation $_entity, OccupationRelations $_relations, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
        $this->relations = $_relations->Where(fn($n) => $n->OccupationId == $_entity->Id);
    }

    public function It(): \API_ProfilingRepositories_Model\Occupation
    {
        $entity = parent::It();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Occupation)
            throw new UnexpectedValueException('Object must be an instance of '.\API_ProfilingRepositories_Model\Occupation::class);

        return $entity;
    }

    public function OccupationRelations(): OccupationRelations
    {
        return $this->relations;
    }
}