<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Permissions;
use API_ProfilingEntities_Collection\Tokens;
use API_ProfilingEntities_Model\Token;
use API_ProfilingRepositories\PermissionRepository;
use API_ProfilingRepositories\TokenRepository;
use API_RelationRepositories\LanguageRelationRepository;
use TS_Exception\Classes\DomainException;

class TokenFactory extends CollectableFactory
{
    private CollectableFactory $factory;
    private Permissions $permissions;

    public function __construct(TokenRepository $repository, PermissionRepository $permissionRepository, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->factory = new CollectableFactory($permissionRepository, $languageRelationRepository);
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        $this->factory->Create();
        $this->permissions = $this->factory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $tokens = [];
        if ($this->collection)
            $tokens = $this->collection->select(fn($n) => new Token($n, $this->permissions))->toArray();

        $this->collectable = new Tokens($tokens);
    }
}