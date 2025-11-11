<?php

namespace API_Invoicing_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\InvoicingException;
use API_Invoicing_Contract\ICustomerService;
use API_InvoicingEntities_Collection\Customers;
use API_InvoicingEntities_Factory\CustomerFactory;
use API_InvoicingEntities_Model\Customer;
use Throwable;
use TS_Exception\Classes\DomainException;

class CustomerService implements ICustomerService
{
    protected CustomerFactory $customerFactory;
    protected Customers $customers;

    public function __construct(CustomerFactory $customerFactory)
    {
        $this->customerFactory = $customerFactory;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getCustomers(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Customer|Customers|null
    {
        if (!isset($this->customers) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->customerFactory->filter($filter, $pageSize, $offset);
            $this->customerFactory->Create();
            $this->customers = $this->customerFactory->collectable();
        }

        if (count($this->customers) === 0)
            return null;

        return $this->customers->count() > 1 ? $this->customers : $this->customers->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function setCustomer(array $data): Customer
    {
        $context = $this->customerFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main customer DTO
            $customer = new \API_InvoicingRepositories_Model\Customer($data['customerData']);
            $this->customerFactory->repository()->add($customer);

            // 2. Get the newly created customer
            $customer = $this->customerFactory->repository()->first([['ProfileId', '=', $data['customerData']['ProfileId']]]);
            if (!$customer)
                throw new InvoicingException('customer_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getCustomers([['Id', '=', $customer->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function putCustomer(string $id, array $data): ?Customer
    {
        $context = $this->customerFactory->repository()->context;
        $context->beginTransaction();

        try{
            $customer = $this->getCustomers([['Id', '=', $id]])?->first();
            if (!$customer)
                throw new InvoicingException('customer_not_found', ["Id" => $id]);

            // 1. Update the main customer record
            foreach ($data as $field => $value)
                $customer->it()->{$field} = $value ?? $customer->it()->{$field};

            $this->customerFactory->repository()->update($customer->it());
            $context->commit();

            return $this->getCustomers([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function deleteCustomer(string $id): bool
    {
        $context = $this->customerFactory->repository()->context;
        $context->beginTransaction();

        try{
            $customer = $this->getCustomers([['Id', '=', $id]])?->first();
            if (!$customer){
                $context->commit();
                return true;
            }

            $this->customerFactory->repository()->deactivate($id);

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
    public function disableCustomer(string $id): bool
    {
        $context = $this->customerFactory->repository()->context;
        $context->beginTransaction();

        try{
            $customer = $this->getCustomers([['Id', '=', $id]])?->first();
            if (!$customer){
                $context->commit();
                return true;
            }

            $this->customerFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}