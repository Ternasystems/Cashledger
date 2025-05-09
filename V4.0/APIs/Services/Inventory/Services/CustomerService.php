<?php

namespace API_Inventory_Service;

use API_Inventory_Contract\ICustomerService;
use API_InventoryEntities_Collection\Customers;
use API_InventoryEntities_Factory\CustomerFactory;
use API_InventoryEntities_Model\Customer;
use API_Profiling_Contract\IProfileService;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Model\Profile;
use Exception;
use ReflectionException;

class CustomerService implements ICustomerService
{
    protected CustomerFactory $customerFactory;
    protected IProfileService $profileService;

    public function __construct(CustomerFactory $_customerFactory, IProfileService $_profileService)
    {
        $this->customerFactory = $_customerFactory;
        $this->profileService = $_profileService;
    }

    public function GetProfiles(callable $predicate = null): Profile|Profiles|null
    {
        return $this->profileService->GetProfiles($predicate);
    }

    /**
     * @throws Exception
     */
    public function GetCustomers(callable $predicate = null): Customer|Customers|null
    {
        $this->customerFactory->Create();

        if (is_null($predicate))
            return $this->customerFactory->Collectable();

        $collection = $this->customerFactory->Collectable()->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     */
    public function SetCustomer(object $model): void
    {
        $repository = $this->customerFactory->Repository();
        $id = null;
        if (empty($model->profileid)){
            $this->profileService->SetProfile($model);
            $id = $this->profileService->GetProfiles(fn($n) => $n->It()->LastName == $model->lastname && $n->It()->BirthDate == $model->birthdate)->It()->Id;
        }else
            $id = $model->profileid;

        $repository->Add(\API_InventoryRepositories_Model\Customer::class, array($id));
    }

    /**
     * @throws ReflectionException
     */
    public function PutCustomer(object $model): void
    {
        $repository = $this->customerFactory->Repository();
        $this->profileService->PutProfile($model);
        $repository->Update(\API_InventoryRepositories_Model\Customer::class, array($model->customerid, $model->desc));
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteCustomer(string $id): void
    {
        $repository = $this->customerFactory->Repository();
        $repository->Remove(\API_InventoryRepositories_Model\Customer::class, array($id));
    }
}