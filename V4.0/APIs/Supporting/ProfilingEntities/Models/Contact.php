<?php

namespace API_ProfilingEntities_Model;

use API_DTOEntities_Model\Entity;
use API_RelationRepositories\ContactRelationRepository;
use API_RelationRepositories_Collection\ContactRelations;
use ReflectionException;
use UnexpectedValueException;

class Contact extends Entity
{
    private ContactType $contactType;
    private ContactRelations $contactRelations;

    /**
     * @throws ReflectionException
     */
    public function __construct(\API_ProfilingRepositories_Model\Contact $_entity, ContactType $_contactType, ContactRelations|ContactRelationRepository $_contactRelations)
    {
        parent::__construct($_entity, null);
        $this->contactType = $_contactType;
        $this->contactRelations = $_contactRelations instanceof ContactRelations ? $_contactRelations->Where(fn($n) => $n->ContactId == $_entity->Id)
            : $_contactRelations->GetBy(fn($n) => $n->ContactId == $_entity->Id);
    }

    public function It(): \API_ProfilingRepositories_Model\Contact
    {
        $entity = parent::It();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Contact)
            throw new UnexpectedValueException('Object must be an instance of '.\API_ProfilingRepositories_Model\Contact::class);

        return $entity;
    }

    public function ContactType(): ContactType
    {
        return $this->contactType;
    }

    public function ContactRelations(): ContactRelations
    {
        return $this->contactRelations;
    }
}