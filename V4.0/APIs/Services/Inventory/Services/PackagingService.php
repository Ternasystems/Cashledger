<?php

namespace API_Inventory_Service;

use API_DTOEntities_Factory\CollectableFactory;
use API_Inventory_Contract\IPackagingService;
use API_InventoryEntities_Collection\Packagings;
use API_InventoryEntities_Model\Packaging;
use API_InventoryRepositories\PackagingRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;

class PackagingService implements IPackagingService
{
    protected Packagings $packagings;
    protected PackagingRepository $packagingRepository;
    protected LanguageRelationRepository $relationRepository;

    /**
     * @throws ReflectionException
     */
    public function __construct(PackagingRepository $_packagings, LanguageRelationRepository $_relationRepository)
    {
        $factory = new CollectableFactory($_packagings, $_relationRepository);
        $factory->Create();
        $this->packagings = $factory->Collectable();
        $this->packagingRepository = $_packagings;
        $this->relationRepository = $_relationRepository;
    }

    public function GetPackagings(callable $predicate = null): Packaging|Packagings|null
    {
        if (is_null($predicate))
            return $this->packagings;

        $collection = $this->packagings->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     */
    public function SetPackaging(object $model): void
    {
        $this->packagingRepository->Add(\API_InventoryRepositories_Model\Packaging::class, array($model->packagingname));
        $factory = new CollectableFactory($this->packagingRepository, $this->relationRepository);
        $factory->Create();
        $this->packagings = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     */
    public function PutPackaging(object $model): void
    {
        $this->packagingRepository->Update(\API_InventoryRepositories_Model\Packaging::class, array($model->packagingid, $model->packagingname));
        $factory = new CollectableFactory($this->packagingRepository, $this->relationRepository);
        $factory->Create();
        $this->packagings = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     */
    public function DeletePackaging(string $id): void
    {
        $this->packagingRepository->Remove(\API_InventoryRepositories_Model\Packaging::class, array($id));
        $factory = new CollectableFactory($this->packagingRepository, $this->relationRepository);
        $factory->Create();
        $this->packagings = $factory->Collectable();
    }
}