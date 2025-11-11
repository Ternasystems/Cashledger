<?php

namespace API_HrmEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_ProfilingEntities_Contract\IProfile;
use API_ProfilingEntities_Model\Profile;

class Employee extends Entity implements IProfile
{
    private Profile $profile;

    public function __construct(\API_HrmRepositories_Model\Employee $_entity, Profile $_profile)
    {
        parent::__construct($_entity);
        $this->profile = $_profile;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_HrmRepositories_Model\Employee
    {
        $entity = parent::it();
        if (!$entity instanceof \API_HrmRepositories_Model\Employee) {
            throw new EntityException('invalid_entity_name', [':name' => \API_HrmRepositories_Model\Employee::class]);
        }

        return $entity;
    }

    public function Profile(): Profile
    {
        return $this->profile;
    }
}