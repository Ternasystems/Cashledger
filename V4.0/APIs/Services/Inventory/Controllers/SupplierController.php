<?php

namespace API_Inventory_Controller;

use API_Inventory_Contract\ISupplierService;
use API_InventoryEntities_Collection\Suppliers;
use API_InventoryEntities_Model\Supplier;
use API_ProfilingEntities_Collection\Profiles;
use TS_Controller\Classes\BaseController;

class SupplierController extends BaseController
{
    private ISupplierService $service;

    public function __construct(ISupplierService $_service)
    {
        $this->service = $_service;
    }

    public function Get(): ?Suppliers
    {
        return $this->service->GetSuppliers();
    }

    public function GetById(string $id): ?Supplier
    {
        return $this->service->GetSuppliers(fn($n) => $n->It()->Id == $id);
    }

    public function GetByName(string $lastName): ?Suppliers
    {
        $profiles = $this->service->GetProfiles(fn($n) => $n->It()->LastName == $lastName);
        return $this->service->GetSuppliers(fn($n) => $profiles->Any(fn($t) => $t->It()->Id == $n->ProfileId));
    }

    public function GetProfiles(): ?Profiles
    {
        $suppliers = $this->service->GetSuppliers();
        return $this->service->GetProfiles(fn($n) => $suppliers->Any(fn($t) => $t->It()->ProfileId) == $n->It()->Id);
    }

    public function Set(object $supplier): void
    {
        $this->service->SetSupplier($supplier);
    }

    public function Put(object $supplier): void
    {
        $this->service->PutSupplier($supplier);
    }

    public function Delete(string $id): void
    {
        $this->service->DeleteSupplier($id);
    }
}