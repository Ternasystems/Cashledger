<?php

namespace API_DTOEntities_Model;

use API_Assets\Classes\EntityException;

class AppCategory extends Entity
{
    /**
     * Initializes a new instance of the AppCategory class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_DTORepositories_Model\AppCategory $_entity The raw AppCategory DTO.
     */
    public function __construct(\API_DTORepositories_Model\AppCategory $_entity)
    {
        parent::__construct($_entity);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_DTORepositories_Model\AppCategory
    {
        $entity = parent::it();
        if (!$entity instanceof \API_DTORepositories_Model\AppCategory) {
            throw new EntityException('invalid_entity_name', [':name' => \API_DTORepositories_Model\AppCategory::class]);
        }

        return $entity;
    }
}