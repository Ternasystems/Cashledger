<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Permissions;
use API_ProfilingEntities_Collection\Tokens;
use API_ProfilingEntities_Model\Token;
use API_ProfilingRepositories\PermissionRepository;
use API_ProfilingRepositories\TokenRepository;
use API_RelationRepositories\LanguageRelationRepository;
use Exception;
use ReflectionException;

class TokenFactory extends CollectableFactory
{
    protected Permissions $permissions;
    protected string $tokenName;

    /**
     * @throws ReflectionException
     */
    public function __construct(TokenRepository $repository, PermissionRepository $_permissions, LanguageRelationRepository $relations)
    {
        parent::__construct($repository, null);
        $factory = new CollectableFactory($_permissions, $relations);
        $factory->Create();
        $this->permissions = $factory->Collectable();
    }

    public function SetTokenName(string $_tokenName): void
    {
        $this->tokenName = $_tokenName;
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $this->repository->SetTableName($this->tokenName);
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item)
            $colArray[] = new Token($item, $this->permissions);

        $this->collectable = new Tokens($colArray);
    }

    public function Collectable(): ?Tokens
    {
        return $this->collectable;
    }

    public function Repository(): TokenRepository
    {
        return $this->repository;
    }
}