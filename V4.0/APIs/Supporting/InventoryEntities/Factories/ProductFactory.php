<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\ProductAttributes;
use API_InventoryEntities_Collection\ProductCategories;
use API_InventoryEntities_Collection\Products;
use API_InventoryEntities_Model\Product;
use API_InventoryRepositories\ProductCategoryRepository;
use API_InventoryRepositories\ProductRepository;
use API_RelationRepositories\LanguageRelationRepository;
use Exception;
use ReflectionException;

class ProductFactory extends CollectableFactory
{
    protected ProductCategories $categories;
    protected ProductAttributes $attributes;

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function __construct(ProductRepository $repository, ProductAttributeFactory $_attributeFactory, ProductCategoryRepository $_categories,
                                LanguageRelationRepository $relations)
    {
        parent::__construct($repository, $relations);
        $_attributeFactory->Create();
        $this->attributes = $_attributeFactory->Collectable();
        $factory = new CollectableFactory($_categories, $relations);
        $factory->Create();
        $this->categories = $factory->Collectable();
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item) {
            $category = $this->categories->FirstOrDefault(fn($n) => $n->It()->Id == $item->CategoryId);
            $attributeArray = [];
            if ($this->attributes->count()){
                foreach ($this->attributes as $attribute) {
                    if (is_null($attribute->AttributeRelations())) continue;
                    foreach ($attribute->AttributeRelations() as $attributeRelation) {
                        if ($attributeRelation->ProductId == $item->Id)
                            $attributeArray[] = $attribute;
                    }
                }
            }
            $_attributes = new ProductAttributes($attributeArray);
            $colArray[] = new Product($item, $category, $_attributes, $this->relationRepository->GetAll());
        }

        $this->collectable = new Products($colArray);
    }

    public function Collectable(): ?Products
    {
        return $this->collectable;
    }

    public function Repository(): ProductRepository
    {
        return $this->repository;
    }
}