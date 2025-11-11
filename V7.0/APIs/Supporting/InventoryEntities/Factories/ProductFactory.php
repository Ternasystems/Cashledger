<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\ProductCategories;
use API_InventoryEntities_Collection\Products;
use API_InventoryEntities_Model\Product;
use API_InventoryRepositories\ProductCategoryRepository;
use API_InventoryRepositories\ProductRepository;
use API_RelationRepositories\LanguageRelationRepository;
use TS_Exception\Classes\DomainException;

class ProductFactory extends CollectableFactory
{
    private CollectableFactory $factory;
    private ProductCategories $categories;

    function __construct(ProductRepository $repository, ProductCategoryRepository $categoryRepository, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->factory = new CollectableFactory($categoryRepository, $languageRelationRepository);
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        $this->factory->Create();
        $this->categories = $this->factory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $products = [];
        if ($this->collection)
            $products = $this->collection->select(fn($n) => new Product($n, $this->categories[$n->CategoryId]))->toArray();

        $this->collectable = new Products($products);
    }
}