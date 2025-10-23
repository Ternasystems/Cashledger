<?php

namespace API_DTOEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTORepositories_Model\Country;

class City extends Entity
{
    private Country $country;

    /**
     * Initializes a new instance of the City class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_DTORepositories_Model\City $_entity The raw City DTO.
     * @param Country $_country The collection of all Countries.
     */
    public function __construct(\API_DTORepositories_Model\City $_entity, Country $_country)
    {
        parent::__construct($_entity);
        $this->country = $_country;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_DTORepositories_Model\City
    {
        $entity = parent::it();
        if (!$entity instanceof \API_DTORepositories_Model\City) {
            throw new EntityException('invalid_entity_name', [':name' => \API_DTORepositories_Model\City::class]);
        }

        return $entity;
    }

    public function Country(): Country
    {
        return $this->country;
    }
}