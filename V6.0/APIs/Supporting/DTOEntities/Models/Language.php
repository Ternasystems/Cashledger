<?php

namespace API_DTOEntities_Model;

use API_Assets\DTOException;

class Language extends Entity
{
    /**
     * Initializes a new instance of the Language class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_DTORepositories_Model\Language $_entity The raw Language DTO.
     */
    public function __construct(\API_DTORepositories_Model\Language $_entity)
    {
        parent::__construct($_entity);
    }

    /**
     * @throws DTOException
     */
    public function It(): \API_DTORepositories_Model\Language
    {
        $entity = parent::It();
        if (!$entity instanceof \API_DTORepositories_Model\Language)
            throw new DTOException('invalid_entity_name');

        return $entity;
    }
}