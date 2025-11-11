<?php

namespace API_Inventory_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\InventoryException;
use API_Inventory_Contract\IProductService;
use API_InventoryEntities_Collection\Products;
use API_InventoryEntities_Factory\ProductFactory;
use API_InventoryEntities_Model\Product;
use Throwable;
use TS_Exception\Classes\DomainException;

class ProductService implements IProductService
{
    protected ProductFactory $productFactory;
    protected Products $products;
    
    function __construct(ProductFactory $productFactory)
    {
        $this->productFactory = $productFactory;
    }
    
    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getProducts(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Product|Products|null
    {
        if (!isset($this->products) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->productFactory->filter($filter, $pageSize, $offset);
            $this->productFactory->Create();
            $this->products = $this->productFactory->collectable();
        }

        if (count($this->products) === 0)
            return null;

        return $this->products->count() > 1 ? $this->products : $this->products->first();
    }

    /**
     * @inheritDoc
     * @throws InventoryException
     * @throws Throwable
     */
    public function SetProduct(array $data): Product
    {
        $context = $this->productFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main product DTO
            $product = new \API_InventoryRepositories_Model\Product($data['productData']);
            $this->productFactory->repository()->add($product);

            // 2. Get the newly created product
            $product = $this->productFactory->repository()->first([['Name', '=', $data['productData']['Name']]]);
            if (!$product)
                throw new InventoryException('product_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getProducts([['Id', '=', $product->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     */
    public function PutProduct(string $id, array $data): ?Product
    {
        $context = $this->productFactory->repository()->context;
        $context->beginTransaction();

        try{
            $product = $this->getProducts([['Id', '=', $id]])?->first();
            if (!$product)
                throw new InventoryException('product_not_found', ["Id" => $id]);

            // 1. Update the main product record
            foreach ($data as $field => $value)
                $product->it()->{$field} = $value ?? $product->it()->{$field};

            $this->productFactory->repository()->update($product->it());
            $context->commit();

            return $this->getProducts([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function DeleteProduct(string $id): bool
    {
        $context = $this->productFactory->repository()->context;
        $context->beginTransaction();

        try{
            $product = $this->getProducts([['Id', '=', $id]])?->first();
            if (!$product){
                $context->commit();
                return true;
            }

            $this->productFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}