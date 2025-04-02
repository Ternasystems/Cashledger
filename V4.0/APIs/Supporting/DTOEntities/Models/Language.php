<?php

namespace API_DTOEntities_Model;

use API_RelationRepositories_Collection\LanguageRelations;
use UnexpectedValueException;

class Language extends Entity
{
    public function __construct(\API_DTORepositories_Model\Language $_entity, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
    }

    public function It(): \API_DTORepositories_Model\Language
    {
        $entity = parent::It();
        if (!$entity instanceof \API_DTORepositories_Model\Language)
            throw new UnexpectedValueException('Object must be an instance of '.\API_DTORepositories_Model\Language::class);

        return $entity;
    }
}