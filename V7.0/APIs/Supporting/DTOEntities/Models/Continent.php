<?php

namespace API_DTOEntities_Model;

use API_Assets\Classes\EntityException;

class Continent extends Entity
{
    /**
     * Initializes a new instance of the Continent class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_DTORepositories_Model\Continent $_entity The raw Continent DTO.
     */
    public function __construct(\API_DTORepositories_Model\Continent $_entity)
    {
        parent::__construct($_entity);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_DTORepositories_Model\Continent
    {
        $entity = parent::it();
        if (!$entity instanceof \API_DTORepositories_Model\Continent) {
            throw new EntityException('invalid_entity_name', [':name' => \API_DTORepositories_Model\Continent::class]);
        }

        return $entity;
    }
}