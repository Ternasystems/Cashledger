<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Roles;
use API_ProfilingEntities_Collection\Tokens;
use API_ProfilingEntities_Model\Role;
use API_ProfilingRepositories\RoleRepository;
use API_RelationRepositories\LanguageRelationRepository;
use TS_Exception\Classes\DomainException;

class RoleFactory extends CollectableFactory
{
    private TokenFactory $factory;
    private Tokens $tokens;

    public function __construct(RoleRepository $repository, TokenFactory $tokens, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->factory = $tokens;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        $this->factory->Create();
        $this->tokens = $this->factory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $roles = [];
        if ($this->collection)
            $roles = $this->collection->select(fn($n) => new Role($n, $this->tokens))->toArray();

        $this->collectable = new Roles($roles);
    }
}