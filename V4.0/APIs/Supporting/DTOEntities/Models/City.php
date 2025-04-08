<?php

namespace API_DTOEntities_Model;

use UnexpectedValueException;

class City extends Entity
{
    private Country $country;

    public function __construct(\API_DTORepositories_Model\City $_entity, Country $_country)
    {
        parent::__construct($_entity, null);
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