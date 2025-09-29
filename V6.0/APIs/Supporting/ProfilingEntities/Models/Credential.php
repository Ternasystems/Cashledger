<?php

namespace API_ProfilingEntities_Model;

use API_Assets\DTOException;
use API_DTOEntities_Model\Entity;

class Credential extends Entity
{
    private Profile $profile;
    private Role $role;

    public function __construct(\API_ProfilingRepositories_Model\Credential $_entity, Profile $_profile, Role $_role)
    {
        parent::__construct($_entity);
        $this->profile = $_profile;
        $this->role = $_role;
    }

    /**
     * @throws DTOException
     */
    public function it(): \API_ProfilingRepositories_Model\Credential
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Credential) {
            throw new DTOException('invalid_entity_name');
        }

        return $entity;
    }

    public function profile(): Profile
    {
        return $this->profile;
    }

    public function role(): Role
    {
        return $this->role;
    }
}