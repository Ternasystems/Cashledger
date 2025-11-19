<?php

namespace API_Inventory_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\InventoryException;
use API_Inventory_Contract\IStockService;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Factory\StockFactory;
use API_InventoryEntities_Model\Stock;
use Throwable;
use TS_Exception\Classes\DomainException;

class StockService implements IStockService
{
    protected StockFactory $stockFactory;
    protected Stocks $stocks;
    
    function __construct(StockFactory $stockFactory)
    {
        $this->stockFactory = $stockFactory;
    }
    
    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getStocks(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Stock|Stocks|null
    {
        if (!isset($this->stocks) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->stockFactory->filter($filter, $pageSize, $offset);
            $this->stockFactory->Create();
            $this->stocks = $this->stockFactory->collectable();
        }

        if (count($this->stocks) === 0)
            return null;

        return $this->stocks->count() > 1 ? $this->stocks : $this->stocks->first();
    }

    /**
     * @inheritDoc
     * @throws InventoryException
     * @throws Throwable
     */
    public function setStock(array $data): Stock
    {
        $context = $this->stockFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main stock DTO
            $stock = new \API_InventoryRepositories_Model\Stock($data['stockData']);
            $this->stockFactory->repository()->add($stock);

            // 2. Get the newly created stock
            $stock = $this->stockFactory->repository()->first([['ProductId', '=', $data['stockData']['ProductId']], ['PackagingId', '=', $data['stockData']['PackagingId']],
                ['BatchNumber', '=', $data['stockData']['BatchNumber']]]);
            if (!$stock)
                throw new InventoryException('stock_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getStocks([['Id', '=', $stock->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function putStock(string $id, array $data): ?Stock
    {
        $context = $this->stockFactory->repository()->context;
        $context->beginTransaction();

        try{
            $stock = $this->getStocks([['Id', '=', $id]])?->first();
            if (!$stock)
                throw new InventoryException('stock_not_found', ["Id" => $id]);

            // 1. Update the main stock record
            foreach ($data as $field => $value)
                $stock->it()->{$field} = $value ?? $stock->it()->{$field};

            $this->stockFactory->repository()->update($stock->it());
            $context->commit();

            return $this->getStocks([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function putQuantity(string $id, float $quantity): ?Stock
    {
        $context = $this->stockFactory->repository()->context;
        $context->beginTransaction();

        try{
            $stock = $this->getStocks([['Id', '=', $id]])?->first();
            if (!$stock)
                throw new InventoryException('stock_not_found', ["Id" => $id]);

            // 1. Update the quantity of the stock record
            $stock->it()->Quantity = $quantity;
            $this->stockFactory->repository()->updateQuantity($stock->it()->Id, $stock->it()->Quantity);
            $context->commit();

            return $this->getStocks([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function deleteStock(string $id): bool
    {
        $context = $this->stockFactory->repository()->context;
        $context->beginTransaction();

        try{
            $stock = $this->getStocks([['Id', '=', $id]])?->first();
            if (!$stock){
                $context->commit();
                return true;
            }

            $this->stockFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}