<?php

namespace API_ProfilingEntities_Model;

use API_Assets\Classes\DTOException;
use API_DTOEntities_Model\Entity;
use API_ProfilingEntities_Collection\Permissions;

class Token extends Entity
{
    private Permissions $permissions;

    /**
     * Initializes a new instance of the Token class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_ProfilingRepositories_Model\Token $_entity The raw Token DTO.
     * @param Permissions $_permissions The collection of all Permissions.
     */
    public function __construct(\API_ProfilingRepositories_Model\Token $_entity, Permissions $_permissions)
    {
        // Call the parent constructor with just the core DTO.
        parent::__construct($_entity);
        $_tokens = explode('-', $_entity->Permissions);
        $this->permissions = $_permissions->Where(fn($n) => in_array($n->It()->Code, $_tokens));
    }

    /**
     * @throws DTOException
     */
    public function it(): \API_ProfilingRepositories_Model\Token
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Token) {
            throw new DTOException('invalid_entity_name', [':name' => \API_ProfilingRepositories_Model\Token::class]);
        }

        return $entity;
    }

    public function Permissions(): Permissions
    {
        return $this->permissions;
    }
}