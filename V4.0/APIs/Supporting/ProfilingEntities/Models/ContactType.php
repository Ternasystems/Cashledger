<?php

namespace API_ProfilingEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\LanguageRelations;
use UnexpectedValueException;

class ContactType extends Entity
{
    public function __construct(\API_ProfilingRepositories_Model\ContactType $_entity, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
    }

    public function It(): \API_ProfilingRepositories_Model\ContactType
    {
        $entity = parent::It();
        if (!$entity instanceof \API_ProfilingRepositories_Model\ContactType)
            throw new UnexpectedValueException('Object must be an instance of '.\API_ProfilingRepositories_Model\ContactType::class);

        return $entity;
    }
}