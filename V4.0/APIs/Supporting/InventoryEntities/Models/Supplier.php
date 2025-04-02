<?php

namespace API_InventoryEntities_Model;

use API_DTOEntities_Model\Entity;
use API_ProfilingEntities_Model\Profile;
use UnexpectedValueException;

class Supplier extends Entity
{
    private Profile $profile;

    public function __construct(\API_InventoryRepositories_Model\Supplier $_entity, Profile $_profile)
    {
        parent::__construct($_entity, null);
        $this->profile = $_profile;
    }

    public function It(): \API_InventoryRepositories_Model\Supplier
    {
        $entity = parent::It();
        if (!$entity instanceof \API_InventoryRepositories_Model\Supplier)
            throw new UnexpectedValueException('Object must be an instance of '.\API_InventoryRepositories_Model\Supplier::class);

        return $entity;
    }

    public function Profile(): Profile
    {
        return $this->profile;
    }
}