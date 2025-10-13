<?php

namespace API_ProfilingEntities_Model;

use API_Assets\Classes\DTOException;
use API_DTOEntities_Model\Entity;

class Permission extends Entity
{
    /**
     * Initializes a new instance of the Permission class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_ProfilingRepositories_Model\Permission $_entity The raw Permission DTO.
     */
    public function __construct(\API_ProfilingRepositories_Model\Permission $_entity)
    {
        // Call the parent constructor with just the core DTO.
        parent::__construct($_entity);
    }

    /**
     * @throws DTOException
     */
    public function it(): \API_ProfilingRepositories_Model\Permission
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Permission) {
            throw new DTOException('invalid_entity_name', [':name' => \API_ProfilingRepositories_Model\Permission::class]);
        }

        return $entity;
    }
}