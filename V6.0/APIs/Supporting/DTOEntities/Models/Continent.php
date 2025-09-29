<?php

namespace API_DTOEntities_Model;

use API_Assets\DTOException;

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
     * @throws DTOException
     */
    public function It(): \API_DTORepositories_Model\Continent
    {
        $entity = parent::It();
        if (!$entity instanceof \API_DTORepositories_Model\Continent)
            throw new DTOException('invalid_entity_name');

        return $entity;
    }
}