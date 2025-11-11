<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\Packagings;
use API_InventoryEntities_Collection\Products;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Collection\Units;
use API_InventoryEntities_Model\Stock;
use API_InventoryRepositories\PackagingRepository;
use API_InventoryRepositories\StockRepository;
use API_InventoryRepositories\UnitRepository;
use API_RelationRepositories\LanguageRelationRepository;
use TS_Exception\Classes\DomainException;

class StockFactory extends CollectableFactory
{
    private ProductFactory $productFactory;
    private Products $products;
    private CollectableFactory $packagingFactory;
    private Packagings $packagings;
    private CollectableFactory $unitFactory;
    private Units $units;

    function __construct(StockRepository $repository, ProductFactory $products, PackagingRepository $packagingRepository, UnitRepository $unitRepository,
                         LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->productFactory = $products;
        $this->packagingFactory = new CollectableFactory($packagingRepository, $languageRelationRepository);
        $this->unitFactory = new CollectableFactory($unitRepository, $languageRelationRepository);
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        $this->productFactory->Create();
        $this->products = $this->productFactory->collectable();

        $this->packagingFactory->Create();
        $this->packagings = $this->packagingFactory->collectable();

        $this->unitFactory->Create();
        $this->units = $this->unitFactory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $stocks = [];
        if ($this->collection)
            $stocks = $this->collection->select(fn($n) => new Stock($n, $this->packagings[$n->PackagingId], $this->products[$n->ProductId], $this->units[$n->UnitId]))->toArray();

        $this->collectable = new Stocks($stocks);
    }
}