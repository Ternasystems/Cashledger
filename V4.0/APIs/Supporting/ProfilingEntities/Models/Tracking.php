<?php

namespace API_ProfilingEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\LanguageRelations;
use UnexpectedValueException;

class Tracking extends Entity
{
    private Credential $credential;

    public function __construct(\API_ProfilingRepositories_Model\Tracking $_entity, Credential $_credential)
    {
        parent::__construct($_entity, null);
        $this->credential = $_credential;
    }

    public function It(): \API_ProfilingRepositories_Model\Tracking
    {
        $entity = parent::It();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Tracking)
            throw new UnexpectedValueException('Object must be an instance of '.\API_ProfilingRepositories_Model\Tracking::class);

        return $entity;
    }

    public function Credential(): Credential
    {
        return $this->credential;
    }

    public function LanguageRelations(): ?LanguageRelations
    {
        return null;
    }
}