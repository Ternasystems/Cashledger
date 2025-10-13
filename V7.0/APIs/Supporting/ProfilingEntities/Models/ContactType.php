<?php

namespace API_ProfilingEntities_Model;

use API_Assets\Classes\DTOException;
use API_DTOEntities_Model\Entity;

class ContactType extends Entity
{
    /**
     * Initializes a new instance of the ContactType class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_ProfilingRepositories_Model\ContactType $_entity The raw ContactType DTO.
     */
    public function __construct(\API_ProfilingRepositories_Model\ContactType $_entity)
    {
        // Call the parent constructor with just the core DTO.
        parent::__construct($_entity);
    }

    /**
     * @throws DTOException
     */
    public function it(): \API_ProfilingRepositories_Model\ContactType
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\ContactType) {
            throw new DTOException('invalid_entity_name', [':name' => \API_ProfilingRepositories_Model\ContactType::class]);
        }

        return $entity;
    }
}