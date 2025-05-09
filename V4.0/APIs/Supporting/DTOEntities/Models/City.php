<?php

namespace API_DTOEntities_Model;

use API_RelationRepositories_Collection\LanguageRelations;
use UnexpectedValueException;

class City extends Entity
{
    private Country $country;

    public function __construct(\API_DTORepositories_Model\City $_entity, Country $_country, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
        $this->country = $_country;
    }

    public function It(): \API_DTORepositories_Model\City
    {
        $entity = parent::It();
        if (!$entity instanceof \API_DTORepositories_Model\City)
            throw new UnexpectedValueException('Object must be an instance of '.\API_DTORepositories_Model\City::class);

        return $entity;
    }

    public function Country(): Country
    {
        return $this->country;
    }
}