<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\Suppliers;
use API_InventoryEntities_Model\Supplier;
use API_InventoryRepositories\SupplierRepository;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Factory\ProfileFactory;
use Exception;

class SupplierFactory extends CollectableFactory
{
    protected Profiles $profiles;

    /**
     * @throws Exception
     */
    public function __construct(SupplierRepository $repository, ProfileFactory $profileFactory)
    {
        parent::__construct($repository, null);
        $profileFactory->Create();
        $this->profiles = $profileFactory->Collectable();
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
            $colArray[] = new Supplier($item, $profile);
        }

        $this->collectable = new Suppliers($colArray);
    }

    public function Collectable(): ?Suppliers
    {
        return $this->collectable;
    }

    public function Repository(): SupplierRepository
    {
        return $this->repository;
    }
}