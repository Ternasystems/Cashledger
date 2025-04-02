<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\Customers;
use API_InventoryEntities_Model\Customer;
use API_InventoryRepositories\CustomerRepository;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Factory\ProfileFactory;
use Exception;

class CustomerFactory extends CollectableFactory
{
    protected Profiles $profiles;

    /**
     * @throws Exception
     */
    public function __construct(CustomerRepository $repository, ProfileFactory $profileFactory)
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
            $colArray[] = new Customer($item, $profile);
        }

        $this->collectable = new Customers($colArray);
    }

    public function Collectable(): ?Customers
    {
        return $this->collectable;
    }

    public function Repository(): CustomerRepository
    {
        return $this->repository;
    }
}