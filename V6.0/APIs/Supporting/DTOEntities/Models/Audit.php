<?php

namespace API_DTOEntities_Model;

use API_Assets\DTOException;

class Audit extends Entity
{
    /**
     * Initializes a new instance of the Audit class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_DTORepositories_Model\Audit $_entity The raw Audit DTO.
     */
    public function __construct(\API_DTORepositories_Model\Audit $_entity)
    {
        parent::__construct($_entity);
    }

    /**
     * @throws DTOException
     */
    public function It(): \API_DTORepositories_Model\Audit
    {
        $entity = parent::It();
        if (!$entity instanceof \API_DTORepositories_Model\Audit)
            throw new DTOException('invalid_entity_name');

        return $entity;
    }
}