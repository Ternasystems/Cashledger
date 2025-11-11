<?php

namespace API_PurchaseEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Factory\ProfileFactory;
use API_PurchaseEntities_Collection\Suppliers;
use API_PurchaseEntities_Model\Supplier;
use API_PurchaseRepositories\SupplierRepository;
use API_RelationRepositories\LanguageRelationRepository;
use TS_Exception\Classes\DomainException;

class SupplierFactory extends CollectableFactory
{
    private ProfileFactory $profileFactory;
    private Profiles $profiles;

    public function __construct(SupplierRepository $repository, ProfileFactory $profileFactory, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->profileFactory = $profileFactory;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->ProfileId)->toArray();
            $this->profileFactory->filter([['ProfileID', 'in', $ids]]);
        }

        $this->profileFactory->Create();
        $this->profiles = $this->profileFactory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $suppliers = [];
        if ($this->collection)
            $suppliers = $this->collection->select(fn($n) => new Supplier($n, $this->profiles[$n->ProfileId]))->toArray();

        $this->collectable = new Suppliers($suppliers);
    }
}