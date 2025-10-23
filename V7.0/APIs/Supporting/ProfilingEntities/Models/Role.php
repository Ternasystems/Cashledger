<?php

namespace API_ProfilingEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_ProfilingEntities_Collection\Tokens;

class Role extends Entity
{
    private Tokens $permissions;

    /**
     * Initializes a new instance of the Role class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_ProfilingRepositories_Model\Role $_entity The raw Role DTO.
     * @param Tokens $_permissions The collection of all Permissions.
     */
    public function __construct(\API_ProfilingRepositories_Model\Role $_entity, Tokens $_permissions)
    {
        // Call the parent constructor with just the core DTO.
        parent::__construct($_entity);
        $this->permissions = $_permissions;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_ProfilingRepositories_Model\Role
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Role) {
            throw new EntityException('invalid_entity_name', [':name' => \API_ProfilingRepositories_Model\Role::class]);
        }

        return $entity;
    }

    public function Tokens(): Tokens
    {
        return $this->permissions;
    }
}