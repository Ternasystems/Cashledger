<?php

namespace API_ProfilingEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_RelationRepositories_Collection\ContactRelations;

class Contact extends Entity
{
    private ContactType $contactType;
    private ContactRelations $relations;

    /**
     * Initializes a new instance of the Contact class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_ProfilingRepositories_Model\Contact $_entity The raw Contact DTO.
     * @param ContactType $_contactType The related ContactType.
     * @param ContactRelations $_relations The collection of all ContactRelations.
     */
    public function __construct(\API_ProfilingRepositories_Model\Contact $_entity, ContactType $_contactType, ContactRelations $_relations)
    {
        parent::__construct($_entity);
        $this->contactType = $_contactType;
        $this->relations = $_relations->where(fn($n)=> $n->ContactId == $_entity->Id);
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_ProfilingRepositories_Model\Contact
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Contact) {
            throw new EntityException('invalid_entity_name', [':name' => \API_ProfilingRepositories_Model\Contact::class]);
        }

        return $entity;
    }

    public function ContactType(): ContactType
    {
        return $this->contactType;
    }

    public function contactRelations(): ContactRelations
    {
        return $this->relations;
    }
}