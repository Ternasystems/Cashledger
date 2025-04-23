<?php

namespace API_Inventory_Service;

use API_Inventory_Contract\ISupplierService;
use API_InventoryEntities_Collection\Suppliers;
use API_InventoryEntities_Factory\SupplierFactory;
use API_InventoryEntities_Model\Supplier;
use API_Profiling_Contract\IProfileService;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Model\Profile;
use Exception;
use ReflectionException;

class SupplierService implements ISupplierService
{
    protected SupplierFactory $supplierFactory;
    protected IProfileService $profileService;

    public function __construct(SupplierFactory $_supplierFactory, IProfileService $_profileService)
    {
        $this->supplierFactory = $_supplierFactory;
        $this->profileService = $_profileService;
    }

    public function GetProfiles(callable $predicate = null): Profile|Profiles|null
    {
        return $this->profileService->GetProfiles($predicate);
    }

    /**
     * @throws Exception
     */
    public function GetSuppliers(callable $predicate = null): Supplier|Suppliers|null
    {
        $this->supplierFactory->Create();

        if (is_null($predicate))
            return $this->supplierFactory->Collectable();

        $collection = $this->supplierFactory->Collectable()->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     */
    public function SetSupplier(object $model): void
    {
        $repository = $this->supplierFactory->Repository();
        $id = null;
        if (empty($model->profileid)){
            $this->profileService->SetProfile($model);
            $id = $this->profileService->GetProfiles(fn($n) => $n->It()->LastName == $model->lastname && $n->Birthdate == $model->birthdate)->FirstOrDefault()->It()->Id;
        }
        $id = $model->profileid;
        $repository->Add(\API_InventoryRepositories_Model\Supplier::class, array($id));
    }

    /**
     * @throws ReflectionException
     */
    public function PutSupplier(object $model): void
    {
        $repository = $this->supplierFactory->Repository();
        $this->profileService->PutProfile($model);
        $repository->Update(\API_InventoryRepositories_Model\Supplier::class, array($model->supplierid, $model->desc));
    }

    /**
     * @throws ReflectionException
     */
    public function DeleteSupplier(string $id): void
    {
        $repository = $this->supplierFactory->Repository();
        $repository->Remove(\API_InventoryRepositories_Model\Supplier::class, array($id));
    }
}