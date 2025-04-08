<?php

namespace API_DTOEntities_Model;

use API_RelationRepositories_Collection\LanguageRelations;
use UnexpectedValueException;

class Country extends Entity
{
    private Continent $continent;

    public function __construct(\API_DTORepositories_Model\Country $_entity, Continent $_continent, ?LanguageRelations $_languageRelations)
    {
        parent::__construct($_entity, $_languageRelations);
        $this->continent = $_continent;
    }

    public function It(): \API_DTORepositories_Model\Country
    {
        $entity = parent::It();
        if (!$entity instanceof \API_DTORepositories_Model\Country)
            throw new UnexpectedValueException('Object must be an instance of '.\API_DTORepositories_Model\Country::class);

        return $entity;
    }

    public function Continent(): Continent
    {
        return $this->continent;
    }
}