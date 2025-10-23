<?php

namespace API_DTOEntities_Model;

use API_Assets\Classes\EntityException;

class Country extends Entity
{
    private Continent $continent;

    /**
     * Initializes a new instance of the Country class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_DTORepositories_Model\Country $_entity The raw Country DTO.
     * @param Continent $_continent The collection of all Countries.
     */
    public function __construct(\API_DTORepositories_Model\Country $_entity, Continent $_continent)
    {
        parent::__construct($_entity);
        $this->continent = $_continent;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_DTORepositories_Model\Country
    {
        $entity = parent::it();
        if (!$entity instanceof \API_DTORepositories_Model\Country) {
            throw new EntityException('invalid_entity_name', [':name' => \API_DTORepositories_Model\Country::class]);
        }

        return $entity;
    }

    public function Continent(): Continent
    {
        return $this->continent;
    }
}