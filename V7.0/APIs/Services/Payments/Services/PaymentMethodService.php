<?php

namespace API_Payments_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\PaymentsException;
use API_DTOEntities_Factory\CollectableFactory;
use API_Payments_Contract\IPaymentMethodService;
use API_PaymentsEntities_Collection\PaymentMethods;
use API_PaymentsEntities_Model\PaymentMethod;
use API_PaymentsRepositories\PaymentMethodRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use Throwable;
use TS_Exception\Classes\DomainException;

class PaymentMethodService implements IPaymentMethodService
{
    protected CollectableFactory $factory;
    protected PaymentMethods $paymentMethods;

    /**
     * @throws ReflectionException
     */
    public function __construct(PaymentMethodRepository $paymentMethodRepository, LanguageRelationRepository $relationRepository)
    {
        $this->factory = new CollectableFactory($paymentMethodRepository, $relationRepository);
    }
    
    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getPaymentMethods(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): PaymentMethod|PaymentMethods|null
    {
        if (!isset($this->paymentMethods) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->paymentMethods = $this->factory->collectable();
        }

        if (count($this->paymentMethods) === 0)
            return null;

        return $this->paymentMethods->count() > 1 ? $this->paymentMethods : $this->paymentMethods->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function SetPaymentMethod(array $data): PaymentMethod
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main paymentMethod DTO
            $paymentMethod = new \API_PaymentsRepositories_Model\PaymentMethod($data['paymentMethodData']);
            $this->factory->repository()->add($paymentMethod);

            // 2. Get the newly created paymentMethod
            $paymentMethod = $this->factory->repository()->first([['Name', '=', $data['paymentMethodData']['Name']]]);
            if (!$paymentMethod)
                throw new PaymentsException('paymentMethod_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getPaymentMethods([['Id', '=', $paymentMethod->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function PutPaymentMethod(string $id, array $data): ?PaymentMethod
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $paymentMethod = $this->getPaymentMethods([['Id', '=', $id]])?->first();
            if (!$paymentMethod)
                throw new PaymentsException('entity_not_found', ["Id" => $id]);

            // 1. Update the main paymentMethod record
            foreach ($data as $field => $value)
                $paymentMethod->it()->{$field} = $value ?? $paymentMethod->it()->{$field};

            $this->factory->repository()->update($paymentMethod->it());
            $context->commit();

            return $this->getPaymentMethods([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function DeletePaymentMethod(string $id): bool
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $paymentMethod = $this->getPaymentMethods([['Id', '=', $id]])?->first();
            if (!$paymentMethod){
                $context->commit();
                return true;
            }

            $this->factory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}