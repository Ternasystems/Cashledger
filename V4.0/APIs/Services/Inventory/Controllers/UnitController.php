<?php

namespace API_Inventory_Controller;

use API_Inventory_Contract\IUnitService;
use API_InventoryEntities_Collection\Units;
use API_InventoryEntities_Model\Unit;
use TS_Controller\Classes\BaseController;

class UnitController extends BaseController
{
    protected IUnitService $service;

    public function __construct(IUnitService $_service)
    {
        $this->service = $_service;
    }

    public function Get(): ?Units
    {
        return $this->service->GetUnits();
    }

    public function GetById(string $id): ?Unit
    {
        return $this->service->GetUnits(fn($n) => $n->It()->Id == $id);
    }

    public function Set(object $Unit): void
    {
        $this->service->SetUnit($Unit);
    }

    public function Put(object $Unit): void
    {
        $this->service->PutUnit($Unit);
    }

    public function Delete(string $id): void
    {
        $this->service->DeleteUnit($id);
    }
}