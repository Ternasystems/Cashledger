<?php

namespace API_PurchaseEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_ProfilingEntities_Contract\IProfile;
use API_ProfilingEntities_Model\Profile;

class Supplier extends Entity implements IProfile
{
    private Profile $profile;

    public function __construct(\API_PurchaseRepositories_Model\Supplier $_entity, Profile $_profile)
    {
        parent::__construct($_entity);
        $this->profile = $_profile;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_PurchaseRepositories_Model\Supplier
    {
        $entity = parent::it();
        if (!$entity instanceof \API_PurchaseRepositories_Model\Supplier) {
            throw new EntityException('invalid_entity_name', [':name' => \API_PurchaseRepositories_Model\Supplier::class]);
        }

        return $entity;
    }

    public function Profile(): Profile
    {
        return $this->profile;
    }
}