<?php

namespace API_Purchase_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\PurchaseException;
use API_Purchase_Contract\ISupplierService;
use API_PurchaseEntities_Collection\Suppliers;
use API_PurchaseEntities_Factory\SupplierFactory;
use API_PurchaseEntities_Model\Supplier;
use Throwable;
use TS_Exception\Classes\DomainException;

class SupplierService implements ISupplierService
{
    protected SupplierFactory $supplierFactory;
    protected Suppliers $suppliers;

    public function __construct(SupplierFactory $supplierFactory)
    {
        $this->supplierFactory = $supplierFactory;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getSuppliers(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Supplier|Suppliers|null
    {
        if (!isset($this->suppliers) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->supplierFactory->filter($filter, $pageSize, $offset);
            $this->supplierFactory->Create();
            $this->suppliers = $this->supplierFactory->collectable();
        }

        if (count($this->suppliers) === 0)
            return null;

        return $this->suppliers->count() > 1 ? $this->suppliers : $this->suppliers->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function setSupplier(array $data): Supplier
    {
        $context = $this->supplierFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main supplier DTO
            $supplier = new \API_PurchaseRepositories_Model\Supplier($data['supplierData']);
            $this->supplierFactory->repository()->add($supplier);

            // 2. Get the newly created supplier
            $supplier = $this->supplierFactory->repository()->first([['ProfileId', '=', $data['supplierData']['ProfileId']]]);
            if (!$supplier)
                throw new PurchaseException('supplier_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getSuppliers([['Id', '=', $supplier->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function putSupplier(string $id, array $data): ?Supplier
    {
        $context = $this->supplierFactory->repository()->context;
        $context->beginTransaction();

        try{
            $supplier = $this->getSuppliers([['Id', '=', $id]])?->first();
            if (!$supplier)
                throw new PurchaseException('supplier_not_found', ["Id" => $id]);

            // 1. Update the main supplier record
            foreach ($data as $field => $value)
                $supplier->it()->{$field} = $value ?? $supplier->it()->{$field};

            $this->supplierFactory->repository()->update($supplier->it());
            $context->commit();

            return $this->getSuppliers([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function deleteSupplier(string $id): bool
    {
        $context = $this->supplierFactory->repository()->context;
        $context->beginTransaction();

        try{
            $supplier = $this->getSuppliers([['Id', '=', $id]])?->first();
            if (!$supplier){
                $context->commit();
                return true;
            }

            $this->supplierFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function disableSupplier(string $id): bool
    {
        $context = $this->supplierFactory->repository()->context;
        $context->beginTransaction();

        try{
            $supplier = $this->getSuppliers([['Id', '=', $id]])?->first();
            if (!$supplier){
                $context->commit();
                return true;
            }

            $this->supplierFactory->repository()->deactivate($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}