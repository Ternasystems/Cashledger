<?php

namespace API_Inventory_Controller;

use API_Inventory_Contract\IManufacturerService;
use API_InventoryEntities_Collection\Manufacturers;
use API_InventoryEntities_Model\Manufacturer;
use TS_Controller\Classes\BaseController;

class ManufacturerController extends BaseController
{
    protected IManufacturerService $service;
    
    public function __construct(IManufacturerService $_service)
    {
        $this->service = $_service;
    }

    public function Get(): ?Manufacturers
    {
        return $this->service->GetManufacturers();
    }

    public function GetById(string $id): ?Manufacturer
    {
        return $this->service->GetManufacturers(fn($n) => $n->It()->Id == $id);
    }

    public function Set(object $Manufacturer): void
    {
        $this->service->SetManufacturer($Manufacturer);
    }

    public function Put(object $Manufacturer): void
    {
        $this->service->PutManufacturer($Manufacturer);
    }

    public function Delete(string $id): void
    {
        $this->service->DeleteManufacturer($id);
    }
}