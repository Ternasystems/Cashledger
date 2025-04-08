<?php

namespace API_Inventory_Service;

use API_DTOEntities_Factory\CollectableFactory;
use API_Inventory_Contract\IProductService;
use API_InventoryEntities_Collection\ProductAttributes;
use API_InventoryEntities_Collection\ProductCategories;
use API_InventoryEntities_Collection\Products;
use API_InventoryEntities_Factory\ProductAttributeFactory;
use API_InventoryEntities_Factory\ProductFactory;
use API_InventoryEntities_Model\Product;
use API_InventoryEntities_Model\ProductAttribute;
use API_InventoryEntities_Model\ProductCategory;
use API_InventoryRepositories\ProductCategoryRepository;
use API_RelationRepositories\LanguageRelationRepository;
use Exception;
use ReflectionException;

class ProductService implements IProductService
{
    protected ?ProductCategories $productCategories;
    protected ProductCategoryRepository $categoryRepository;
    protected ProductFactory $productFactory;
    protected ProductAttributeFactory $attributeFactory;
    protected LanguageRelationRepository $relationRepository;

    /**
     * @throws ReflectionException
     */
    public function __construct(ProductCategoryRepository $_categoryRepository, ProductFactory $_productFactory, ProductAttributeFactory $_attributeFactory,
                                LanguageRelationRepository $_relationRepository)
    {
        $factory = new CollectableFactory($_categoryRepository, $_relationRepository);
        $factory->Create();
        $this->productCategories = $factory->Collectable();
        $this->categoryRepository = $_categoryRepository;
        $this->relationRepository = $_relationRepository;
        //
        $this->productFactory = $_productFactory;
        $this->attributeFactory = $_attributeFactory;
    }

    public function GetCategories(callable $predicate = null): ProductCategory|ProductCategories|null
    {
        if (is_null($predicate))
            return $this->productCategories;

        $collection = $this->productCategories->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     */
    public function SetCategory(object $model): void
    {
        $this->categoryRepository->Add(\API_InventoryRepositories_Model\ProductCategory::class, array($model->categoryname, $model->categorydesc));
        $factory = new CollectableFactory($this->categoryRepository, $this->relationRepository);
        $factory->Create();
        $this->productCategories = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     */
    public function PutCategory(object $model): void
    {
        $this->categoryRepository->Update(\API_InventoryRepositories_Model\ProductCategory::class, array($model->categoryid, $model->categoryname,
            $model->categorydesc));
        $factory = new CollectableFactory($this->categoryRepository, $this->relationRepository);
        $factory->Create();
        $this->productCategories = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteCategory(string $id): void
    {
        $this->categoryRepository->Remove(\API_InventoryRepositories_Model\ProductCategory::class, array($id));
        $factory = new CollectableFactory($this->categoryRepository, $this->relationRepository);
        $factory->Create();
        $this->productCategories = $factory->Collectable();
    }

    /**
     * @throws Exception
     */
    public function GetAttributes(callable $predicate = null): ProductAttribute|ProductAttributes|null
    {
        $this->attributeFactory->Create();

        if (is_null($predicate))
            return $this->attributeFactory->Collectable();

        $collection = $this->attributeFactory->Collectable()->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     */
    public function SetAttribute(object $model): void
    {
        $repository = $this->attributeFactory->Repository();
        $repository->Add(\API_InventoryRepositories_Model\ProductAttribute::class, array($model->attributename, $model->attributetype, $model->attributeconstraint,
            $model->attributedesc));
    }

    /**
     * @throws ReflectionException
     */
    public function PutAttribute(object $model): void
    {
        $repository = $this->attributeFactory->Repository();
        $repository->Update(\API_InventoryRepositories_Model\ProductAttribute::class, array($model->attributeid, $model->attributename, $model->attributetype,
            $model->attributeconstraint, $model->attributedesc));
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteAttribute(string $id): void
    {
        $repository = $this->attributeFactory->Repository();
        $repository->Remove(\API_InventoryRepositories_Model\ProductAttribute::class, array($id));
    }

    /**
     * @throws Exception
     */
    public function GetProducts(callable $predicate = null): Product|Products|null
    {
        $this->productFactory->Create();

        if (is_null($predicate))
            return $this->productFactory->Collectable();

        $collection = $this->productFactory->Collectable()->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function SetProduct(object $model): void
    {
        $repository = $this->productFactory->Repository();
        $repository->Add(\API_InventoryRepositories_Model\Product::class, array($model->productname, $model->categoryid, $model->unitid, $model->minstock,
            $model->maxstock, $model->product->desc));
    }

    public function PutProduct(object $model): void
    {
        // TODO: Implement PutProduct() method.
    }

    public function DeleteProduct(string $id): void
    {
        // TODO: Implement DeleteProduct() method.
    }
}