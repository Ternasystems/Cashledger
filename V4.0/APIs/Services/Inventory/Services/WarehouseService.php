<?php

namespace API_Inventory_Service;

use API_DTOEntities_Factory\CollectableFactory;
use API_Inventory_Contract\IWarehouseService;
use API_InventoryEntities_Collection\Warehouses;
use API_InventoryEntities_Model\Warehouse;
use API_InventoryRepositories\WarehouseRepository;
use ReflectionException;

class WarehouseService implements IWarehouseService
{
    protected Warehouses $warehouses;
    protected WarehouseRepository $warehouseRepository;

    /**
     * @throws ReflectionException
     */
    public function __construct(WarehouseRepository $_warehouses)
    {
        $factory = new CollectableFactory($_warehouses, null);
        $factory->Create();
        $this->warehouses = $factory->Collectable();
        $this->warehouseRepository = $_warehouses;
    }

    public function GetWarehouses(callable $predicate = null): Warehouse|Warehouses|null
    {
        if (is_null($predicate))
            return $this->warehouses;

        $collection = $this->warehouses->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     */
    public function SetWarehouse(object $model): void
    {
        $this->warehouseRepository->Add(\API_InventoryRepositories_Model\Warehouse::class, array($model->warehousename, $model->warehouselocation,
            $model->warehousedesc));
        $factory = new CollectableFactory($this->warehouseRepository, null);
        $factory->Create();
        $this->warehouses = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     */
    public function PutWarehouse(object $model): void
    {
        $this->warehouseRepository->Update(\API_InventoryRepositories_Model\Warehouse::class, array($model->warehouseid, $model->warehousename,
            $model->warehouselocation, $model->warehousedesc));
        $factory = new CollectableFactory($this->warehouseRepository, null);
        $factory->Create();
        $this->warehouses = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteWarehouse(string $id): void
    {
        $this->warehouseRepository->Remove(\API_InventoryRepositories_Model\Warehouse::class, array($id));
        $factory = new CollectableFactory($this->warehouseRepository, null);
        $factory->Create();
        $this->warehouses = $factory->Collectable();
    }
}