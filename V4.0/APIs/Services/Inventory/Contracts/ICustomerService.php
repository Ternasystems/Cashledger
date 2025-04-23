<?php

namespace API_Inventory_Contract;

use API_InventoryEntities_Collection\Customers;
use API_InventoryEntities_Model\Customer;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Model\Profile;

interface ICustomerService
{
    public function GetProfiles(callable $predicate = null): Profile|Profiles|null;
    public function GetCustomers(callable $predicate = null): Customer|Customers|null;
    public function SetCustomer(object $model): void;
    public function PutCustomer(object $model): void;
    public function DeleteCustomer(string $id): void;
}