<?php

namespace API_Inventory_Service;

use API_DTOEntities_Factory\CollectableFactory;
use API_Inventory_Contract\IManufacturerService;
use API_InventoryEntities_Collection\Manufacturers;
use API_InventoryEntities_Model\Manufacturer;
use API_InventoryRepositories\ManufacturerRepository;
use ReflectionException;

class ManufacturerService implements IManufacturerService
{
    protected Manufacturers $manufacturers;
    protected ManufacturerRepository $manufacturerRepository;

    /**
     * @throws ReflectionException
     */
    public function __construct(ManufacturerRepository $_manufacturers)
    {
        $factory = new CollectableFactory($_manufacturers, null);
        $factory->Create();
        $this->manufacturers = $factory->Collectable();
        $this->manufacturerRepository = $_manufacturers;
    }

    public function GetManufacturers(callable $predicate = null): Manufacturer|Manufacturers|null
    {
        if (is_null($predicate))
            return $this->manufacturers;

        $collection = $this->manufacturers->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     */
    public function SetManufacturer(object $model): void
    {
        $this->manufacturerRepository->Add(\API_InventoryRepositories_Model\Manufacturer::class, array($model->manufacturername, $model->manufacturerdesc));
        $factory = new CollectableFactory($this->manufacturerRepository, null);
        $factory->Create();
        $this->manufacturers = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     */
    public function PutManufacturer(object $model): void
    {
        $this->manufacturerRepository->Update(\API_InventoryRepositories_Model\Manufacturer::class, array($model->manufacturerid, $model->manufacturername,
            $model->manufacturerdesc));
        $factory = new CollectableFactory($this->manufacturerRepository, null);
        $factory->Create();
        $this->manufacturers = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteManufacturer(string $id): void
    {
        $this->manufacturerRepository->Remove(\API_InventoryRepositories_Model\Manufacturer::class, array($id));
        $factory = new CollectableFactory($this->manufacturerRepository, null);
        $factory->Create();
        $this->manufacturers = $factory->Collectable();
    }
}