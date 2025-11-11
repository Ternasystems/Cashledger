<?php

namespace API_TellerEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_ProfilingEntities_Model\Profile;

class Teller extends Entity
{
    private Profile $profile;

    /**
     * Initializes a new instance of the AppCategory class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_TellerRepositories_Model\Teller $_entity The raw AppCategory DTO.
     */
    public function __construct(\API_TellerRepositories_Model\Teller $_entity, Profile $_profile)
    {
        parent::__construct($_entity);
        $this->profile = $_profile;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_TellerRepositories_Model\Teller
    {
        $entity = parent::it();
        if (!$entity instanceof \API_TellerRepositories_Model\Teller) {
            throw new EntityException('invalid_entity_name', [':name' => \API_TellerRepositories_Model\Teller::class]);
        }

        return $entity;
    }

    public function Profile(): Profile
    {
        return $this->profile;
    }
}