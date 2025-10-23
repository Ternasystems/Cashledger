<?php

namespace API_DTOEntities_Model;

use API_Assets\Classes\EntityException;

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
     * @throws EntityException
     */
    public function it(): \API_DTORepositories_Model\Language
    {
        $entity = parent::it();
        if (!$entity instanceof \API_DTORepositories_Model\Language) {
            throw new EntityException('invalid_entity_name', [':name' => \API_DTORepositories_Model\Language::class]);
        }

        return $entity;
    }
}