<?php

namespace API_ProfilingEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;

class Tracking extends Entity
{
    private Credential $credential;

    public function __construct(\API_ProfilingRepositories_Model\Tracking $_entity, Credential $_credential)
    {
        parent::__construct($_entity);
        $this->credential = $_credential;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_ProfilingRepositories_Model\Tracking
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Tracking) {
            throw new EntityException('invalid_entity_name', [':name' => \API_ProfilingRepositories_Model\Tracking::class]);
        }

        return $entity;
    }

    public function Credential(): Credential
    {
        return $this->credential;
    }
}