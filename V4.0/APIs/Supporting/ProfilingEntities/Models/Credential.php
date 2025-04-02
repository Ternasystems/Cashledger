<?php

namespace API_ProfilingEntities_Model;

use API_DTOEntities_Model\Entity;
use UnexpectedValueException;

class Credential extends Entity
{
    private Profile $profile;
    private Role $role;

    public function __construct(\API_ProfilingRepositories_Model\Credential $_entity, Profile $_profile, Role $_role)
    {
        parent::__construct($_entity, null);
        $this->profile = $_profile;
        $this->role = $_role;
    }

    public function It(): \API_ProfilingRepositories_Model\Credential
    {
        $entity = parent::It();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Credential)
            throw new UnexpectedValueException('Object must be an instance of '.\API_ProfilingRepositories_Model\Credential::class);

        return $entity;
    }

    public function Profile(): Profile
    {
        return $this->profile;
    }

    public function Role(): Role
    {
        return $this->role;
    }
}