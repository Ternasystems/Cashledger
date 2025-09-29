<?php

namespace API_ProfilingEntities_Model;

use API_Assets\DTOException;
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
     * @throws DTOException
     */
    public function it(): \API_ProfilingRepositories_Model\Tracking
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Tracking) {
            throw new DTOException('invalid_entity_name');
        }

        return $entity;
    }

    public function credential(): Credential
    {
        return $this->credential;
    }
}