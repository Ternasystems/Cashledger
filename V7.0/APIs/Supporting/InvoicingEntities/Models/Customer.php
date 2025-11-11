<?php

namespace API_InvoicingEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_ProfilingEntities_Contract\IProfile;
use API_ProfilingEntities_Model\Profile;

class Customer extends Entity implements IProfile
{
    private Profile $profile;

    public function __construct(\API_InvoicingRepositories_Model\Customer $_entity, Profile $_profile)
    {
        parent::__construct($_entity);
        $this->profile = $_profile;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_InvoicingRepositories_Model\Customer
    {
        $entity = parent::it();
        if (!$entity instanceof \API_InvoicingRepositories_Model\Customer) {
            throw new EntityException('invalid_entity_name', [':name' => \API_InvoicingRepositories_Model\Customer::class]);
        }

        return $entity;
    }

    public function Profile(): Profile
    {
        return $this->profile;
    }
}