<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Credentials;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Collection\Roles;
use API_ProfilingEntities_Model\Credential;
use API_ProfilingRepositories\CredentialRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories\RoleRelationRepository;
use API_RelationRepositories_Collection\RoleRelations;
use TS_Exception\Classes\DomainException;

class CredentialFactory extends CollectableFactory
{
    private RoleRelationRepository $relationRepository;
    private RoleRelations $roleRelations;
    private ProfileFactory $profileFactory;
    private Profiles $profiles;
    private RoleFactory $roleFactory;
    private Roles $roles;

    public function __construct(CredentialRepository $repository, ProfileFactory $profileFactory, RoleFactory $roleFactory, RoleRelationRepository $relationRepository,
                                LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->relationRepository = $relationRepository;
        $this->profileFactory = $profileFactory;
        $this->roleFactory = $roleFactory;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        $relations = [];
        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->Id)->toArray();
            $relations = $this->relationRepository->getBy([['CredentialID', 'in', $ids]]);
        }
        $this->roleRelations = new RoleRelations($relations);

        $this->roleFactory->Create();
        $this->roles = $this->roleFactory->collectable();

        $this->profileFactory->Create();
        $this->profiles = $this->profileFactory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $credentials = [];
        if ($this->collection)
            $credentials = $this->collection->select(fn($n) => new Credential($n, $this->profiles[$n->ProfileId], $this->roles[$n->RoleId]))->toArray();

        $this->collectable = new Credentials($credentials);
    }
}