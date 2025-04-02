<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Roles;
use API_ProfilingEntities_Model\Role;
use API_ProfilingRepositories\RoleRepository;
use API_RelationRepositories\LanguageRelationRepository;
use Exception;

class RoleFactory extends CollectableFactory
{
    protected array $permissions;

    /**
     * @throws Exception
     */
    public function __construct(RoleRepository $repository, TokenFactory $tokenFactory, LanguageRelationRepository $relations)
    {
        parent::__construct($repository, $relations);

        $roles = $repository->GetAll();
        foreach ($roles as $role) {
            $tokenFactory->SetTokenName($role->Name);
            $tokenFactory->Create();
            $this->permissions[$role->Name] = $tokenFactory->Collectable();
        }
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item)
            $colArray[] = new Role($item, $this->permissions[$item->Name]->FirstOrDefault(fn($n) => $n->It()->RoleId == $item->Id), $this->relationRepository->GetAll());

        $this->collectable = new Roles($colArray);
    }

    public function Collectable(): ?Roles
    {
        return $this->collectable;
    }

    public function Repository(): RoleRepository
    {
        return $this->repository;
    }
}