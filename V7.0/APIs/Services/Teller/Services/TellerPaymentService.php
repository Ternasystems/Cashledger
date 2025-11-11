<?php

namespace API_Teller_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\TellerException;
use API_Teller_Contract\ITellerPaymentService;
use API_TellerEntities_Collection\TellerPayments;
use API_TellerEntities_Factory\TellerPaymentFactory;
use API_TellerEntities_Model\TellerPayment;
use Throwable;
use TS_Exception\Classes\DomainException;

class TellerPaymentService implements ITellerPaymentService
{
    protected TellerPaymentFactory $paymentFactory;
    protected TellerPayments $payments;

    public function __construct(TellerPaymentFactory $paymentFactory)
    {
        $this->paymentFactory = $paymentFactory;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getTellerPayments(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerPayment|TellerPayments|null
    {
        if (!isset($this->payments) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->paymentFactory->filter($filter, $pageSize, $offset);
            $this->paymentFactory->Create();
            $this->payments = $this->paymentFactory->collectable();
        }

        if (count($this->payments) === 0)
            return null;

        return $this->payments->count() > 1 ? $this->payments : $this->payments->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function SetTellerPayment(array $data): TellerPayment
    {
        $context = $this->paymentFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main payment DTO
            $payment = new \API_TellerRepositories_Model\TellerPayment($data['paymentData']);
            $this->paymentFactory->repository()->add($payment);

            // 2. Get the newly created payment
            $payment = $this->paymentFactory->repository()->first([['PaymentId', '=', $data['paymentData']['PaymentId']], ['TransactionId', '=', $data['paymentData']['TransactionId']]]);
            if (!$payment)
                throw new TellerException('payment_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getTellerPayments([['Id', '=', $payment->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}