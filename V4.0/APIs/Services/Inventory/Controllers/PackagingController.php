<?php

namespace API_Inventory_Controller;

use API_Inventory_Contract\IPackagingService;
use API_InventoryEntities_Collection\Packagings;
use API_InventoryEntities_Model\Packaging;
use TS_Controller\Classes\BaseController;

class PackagingController extends BaseController
{
    protected IPackagingService $service;

    public function __construct(IPackagingService $_service)
    {
        $this->service = $_service;
    }

    public function Get(): ?Packagings
    {
        return $this->service->GetPackagings();
    }

    public function GetById(string $id): ?Packaging
    {
        return $this->service->GetPackagings(fn($n) => $n->It()->Id == $id);
    }

    public function Set(object $Packaging): void
    {
        $this->service->SetPackaging($Packaging);
    }

    public function Put(object $Packaging): void
    {
        $this->service->PutPackaging($Packaging);
    }

    public function Delete(string $id): void
    {
        $this->service->DeletePackaging($id);
    }
}