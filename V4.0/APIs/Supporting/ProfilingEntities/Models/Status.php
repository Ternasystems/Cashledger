<?php

namespace API_ProfilingEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\LanguageRelations;
use API_RelationRepositories_Collection\StatusRelations;
use http\Exception\UnexpectedValueException;

class Status extends Entity
{
    private StatusRelations $relations;

    public function __construct(\API_ProfilingRepositories_Model\Status $_entity, StatusRelations $_relations, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
        $this->relations = $_relations->Where(fn($n) => $n->StatusId == $_entity->Id);
    }

    public function It(): \API_ProfilingRepositories_Model\Status
    {
        $entity = parent::It();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Status)
            throw new UnexpectedValueException('Object must be an instance of '.\API_ProfilingRepositories_Model\Status::class);

        return $entity;
    }

    public function StatusRelations(): StatusRelations
    {
        return $this->relations;
    }
}