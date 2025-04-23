<?php

namespace API_Inventory_Service;

use API_Administration_Contract\ILanguageService;
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
use API_RelationRepositories\AttributeRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Model\AttributeRelation;
use API_RelationRepositories_Model\LanguageRelation;
use Exception;
use ReflectionException;
use TS_Domain\Classes\Linq;

class ProductService implements IProductService
{
    protected ?ProductCategories $productCategories;
    protected ProductCategoryRepository $categoryRepository;
    protected ProductFactory $productFactory;
    protected ProductAttributeFactory $attributeFactory;
    protected LanguageRelationRepository $relationRepository;
    protected AttributeRelationRepository $attributeRepository;
    protected ILanguageService $languageService;

    /**
     * @throws ReflectionException
     */
    public function __construct(ProductCategoryRepository $_categoryRepository, ProductFactory $_productFactory, ProductAttributeFactory $_attributeFactory,
                                AttributeRelationRepository $_attributeRepository, LanguageRelationRepository $_relationRepository, ILanguageService $_languageService)
    {
        $factory = new CollectableFactory($_categoryRepository, $_relationRepository);
        $factory->Create();
        $this->productCategories = $factory->Collectable();
        $this->categoryRepository = $_categoryRepository;
        $this->relationRepository = $_relationRepository;
        $this->attributeRepository = $_attributeRepository;
        //
        $this->productFactory = $_productFactory;
        $this->attributeFactory = $_attributeFactory;
        $this->languageService = $_languageService;
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
        $id = $this->productCategories->FirstOrDefault(fn($n) => $n->It()->Name == $model->categoryname)->It()->Id;
        //
        $languages = $this->languageService->GetLanguages();
        foreach ($languages as $language){
            $lang = $language->It()->Label;
            $this->relationRepository->Add(LanguageRelation::class, array($language->It()->Id, $id, $model->categorylocale[$lang]));
        }
        //
        $factory = new CollectableFactory($this->categoryRepository, $this->relationRepository);
        $factory->Create();
        $this->productCategories = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function PutCategory(object $model): void
    {
        $this->categoryRepository->Update(\API_InventoryRepositories_Model\ProductCategory::class, array($model->categoryid, $model->categoryname,
            $model->categorydesc));
        //
        $languages = $this->languageService->GetLanguages();
        $relations = $this->productCategories->FirstOrDefault(fn($n) => $n->It()->Id == $model->categoryid)->LanguageRelations();
        foreach ($relations as $relation){
            $id = $relation->LangId;
            $lang = $languages->FirstOrDefault(fn($n) => $n->It()->Id == $id)->It()->Label;
            if (key_exists($lang, $model->categorylocale))
                $this->relationRepository->Update(LanguageRelation::class, array($relation->Id, $model->categorylocale[$lang]));
            else
                $this->relationRepository->Remove(LanguageRelation::class, array($relation->Id));
        }
        //
        $factory = new CollectableFactory($this->categoryRepository, $this->relationRepository);
        $factory->Create();
        $this->productCategories = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function DeleteCategory(string $id): void
    {
        $relations = $this->productCategories->FirstOrDefault(fn($n) => $n->It()->Id == $id)->LanguageRelations();
        foreach ($relations as $relation)
            $this->relationRepository->Remove(LanguageRelation::class, array($relation->Id));
        //
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
     * @throws Exception
     */
    public function SetAttribute(object $model): void
    {
        $linq = new Linq();
        $constraint = $linq->constraint($model->constrainttype, $model->attributeconstraint);
        $table = $linq->constraintTable($constraint);
        $repository = $this->attributeFactory->Repository();
        $repository->Add(\API_InventoryRepositories_Model\ProductAttribute::class, array($model->attributename, $model->attributetype, $constraint, $table,
            $model->attributedesc));
        $this->attributeFactory->Create();
        $id = $this->attributeFactory->Collectable()->FirstOrDefault(fn($n) => $n->It()->Name == $model->attributename)->It()->Id;
        //
        $languages = $this->languageService->GetLanguages();
        foreach ($languages as $language){
            $lang = $language->It()->Label;
            $this->relationRepository->Add(LanguageRelation::class, array($language->It()->Id, $id, $model->attributelocale[$lang]));
        }
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function PutAttribute(object $model): void
    {
        $linq = new Linq();
        $constraint = $linq->constraint($model->constrainttype, $model->attributeconstraint);
        $table = $linq->constraintTable($constraint);
        $repository = $this->attributeFactory->Repository();
        $repository->Update(\API_InventoryRepositories_Model\ProductAttribute::class, array($model->attributeid, $model->attributename, $model->attributetype, $constraint,
            $table ,$model->attributedesc));
        //
        $languages = $this->languageService->GetLanguages();
        $this->attributeFactory->Create();
        $relations = $this->attributeFactory->Collectable()->FirstOrDefault(fn($n) => $n->It()->Id == $model->attributeid)->LanguageRelations();
        foreach ($relations as $relation){
            $id = $relation->LangId;
            $lang = $languages->FirstOrDefault(fn($n) => $n->It()->Id == $id)->It()->Label;
            if (key_exists($lang, $model->attributelocale))
                $this->relationRepository->Update(LanguageRelation::class, array($relation->Id, $model->attributelocale[$lang]));
            else
                $this->relationRepository->Remove(LanguageRelation::class, array($relation->Id));
        }
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function DeleteAttribute(string $id): void
    {
        $relations = $this->attributeFactory->Collectable()->FirstOrDefault(fn($n) => $n->It()->Id == $id)->LanguageRelations();
        foreach ($relations as $relation)
            $this->relationRepository->Remove(LanguageRelation::class, array($relation->Id));
        //
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
            $model->maxstock, $model->productdesc));
        $this->productFactory->Create();
        $id = $this->productFactory->Collectable()->FirstOrDefault(fn($n) => $n->It()->Name == $model->productname)->It()->Id;
        //
        $languages = $this->languageService->GetLanguages();
        foreach ($languages as $language){
            $lang = $language->It()->Label;
            if (key_exists($lang, $model->productlocale))
                $this->relationRepository->Add(LanguageRelation::class, array($language->It()->Id, $id, $model->productlocale[$lang]));
        }
        //
        if (count($model->attributes) == 0)
            return;

        foreach ($model->attributes as $key => $attribute)
            $this->attributeRepository->Add(AttributeRelation::class, array($key, $id, $attribute));
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function PutProduct(object $model): void
    {
        $repository = $this->productFactory->Repository();
        $repository->Update(\API_InventoryRepositories_Model\Product::class, array($model->productid, $model->productname, $model->unitid, $model->minstock,
            $model->maxstock, $model->productdesc));
        //
        $this->productFactory->Create();
        $languages = $this->languageService->GetLanguages();
        $relations = $this->productFactory->Collectable()->FirstOrDefault(fn($n) => $n->It()->Id == $model->productid)->LanguageRelations();
        foreach ($relations as $relation){
            $id = $relation->LangId;
            $lang = $languages->FirstOrDefault(fn($n) => $n->It()->Id == $id)->It()->Label;
            if (key_exists($lang, $model->productlocale))
                $this->relationRepository->Update(LanguageRelation::class, array($relation->Id, $model->productlocale[$lang]));
            else
                $this->relationRepository->Remove(LanguageRelation::class, array($relation->Id));
        }
        //
        foreach ($this->attributeRepository->GetBy(fn($n) => $n->ProductId == $model->productid) as $attributeRelation)
            $this->attributeRepository->Remove(AttributeRelation::class, array($attributeRelation->Id));
        //
        foreach ($model->attributes as $key => $attribute)
            $this->attributeRepository->Add(AttributeRelation::class, array($key, $model->productid, $attribute));
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function DeleteProduct(string $id): void
    {
        $this->productFactory->Create();
        $relations = $this->productFactory->Collectable()->FirstOrDefault(fn($n) => $n->It()->Id == $id)->LanguageRelations();
        foreach ($relations as $relation)
            $this->relationRepository->Remove(LanguageRelation::class, array($relation->Id));
        //
        $attributes = $this->productFactory->Collectable()->FirstOrDefault(fn($n) => $n->It()->Id == $id)->ProductAttributes();
        foreach ($attributes as $attribute){
            $attributeRelations = $attribute->AttributeRelations()->Where(fn($n) => $n->ProductId == $id);
            foreach ($attributeRelations as $attributeRelation)
                $this->attributeRepository->Remove(AttributeRelation::class, array($attributeRelation->Id));
        }
        //
        $repository = $this->productFactory->Repository();
        $repository->Remove(\API_InventoryRepositories_Model\Product::class, array($id));
    }
}