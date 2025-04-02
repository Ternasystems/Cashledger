<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Credentials;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Collection\Roles;
use API_ProfilingEntities_Model\Credential;
use API_ProfilingRepositories\CredentialRepository;
use API_RelationRepositories\RoleRelationRepository;
use Exception;

class CredentialFactory extends CollectableFactory
{
    protected Profiles $profiles;
    protected Roles $roles;
    protected RoleRelationRepository $roleRelations;

    /**
     * @throws Exception
     */
    public function __construct(CredentialRepository $repository, ProfileFactory $profileFactory, RoleFactory $roleFactory, RoleRelationRepository $_roleRelations)
    {
        parent::__construct($repository, null);
        $profileFactory->Create();
        $this->profiles = $profileFactory->Collectable();
        $roleFactory->Create();
        $this->roles = $roleFactory->Collectable();
        $this->roleRelations = $_roleRelations;
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item) {
            $profile = $this->profiles->FirstOrDefault(fn($n) => $n->It()->Id == $item->ProfileId);
            $roleRelation = $this->roleRelations->FirstOrDefault(fn($n) => $n->CredentialId == $item->Id);
            $role = $this->roles->FirstOrDefault(fn($n) => $n->It()->Id == $roleRelation->RoleId);
            $colArray[] = new Credential($item, $profile, $role);
        }

        $this->collectable = new Credentials($colArray);
    }

    public function Collectable(): ?Credentials
    {
        return $this->collectable;
    }

    public function Repository(): CredentialRepository
    {
        return $this->repository;
    }
}