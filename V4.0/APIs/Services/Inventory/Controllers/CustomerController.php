<?php

namespace API_Inventory_Controller;

use API_Inventory_Contract\ICustomerService;
use API_InventoryEntities_Collection\Customers;
use API_InventoryEntities_Model\Customer;
use API_ProfilingEntities_Collection\Profiles;
use TS_Controller\Classes\BaseController;

class CustomerController extends BaseController
{
    private ICustomerService $service;

    public function __construct(ICustomerService $_service)
    {
        $this->service = $_service;
    }

    public function Get(): ?Customers
    {
        return $this->service->GetCustomers();
    }

    public function GetById(string $id): ?Customer
    {
        return $this->service->GetCustomers(fn($n) => $n->It()->Id == $id);
    }

    public function GetByName(string $lastName): ?Customers
    {
        $profiles = $this->service->GetProfiles(fn($n) => $n->It()->LastName == $lastName);
        return $this->service->GetCustomers(fn($n) => $profiles->Any(fn($t) => $t->It()->Id == $n->ProfileId));
    }

    public function GetProfiles(): ?Profiles
    {
        $customers = $this->service->GetCustomers();
        return $this->service->GetProfiles(fn($n) => $customers->Any(fn($t) => $t->It()->ProfileId) == $n->It()->Id);
    }

    public function Set(object $customer): void
    {
        $this->service->SetCustomer($customer);
    }

    public function Put(object $customer): void
    {
        $this->service->PutCustomer($customer);
    }

    public function Delete(string $id): void
    {
        $this->service->DeleteCustomer($id);
    }
}