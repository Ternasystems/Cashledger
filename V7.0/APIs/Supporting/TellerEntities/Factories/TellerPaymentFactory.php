<?php

namespace API_TellerEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_PaymentsEntities_Collection\PaymentMethods;
use API_PaymentsRepositories\PaymentMethodRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_TellerEntities_Collection\TellerPayments;
use API_TellerEntities_Collection\Tellers;
use API_TellerEntities_Collection\TellerTransactions;
use API_TellerEntities_Model\TellerPayment;
use API_TellerRepositories\TellerPaymentRepository;
use TS_Exception\Classes\DomainException;

class TellerPaymentFactory extends CollectableFactory
{
    private TellerFactory $tellerFactory;
    private Tellers $tellers;
    private TellerTransactionFactory $transactionFactory;
    private TellerTransactions $transactions;
    private CollectableFactory $factory;
    private PaymentMethods $paymentMethods;

    public function __construct(TellerPaymentRepository $repository, TellerFactory $tellerFactory, TellerTransactionFactory $transactionFactory,
                                PaymentMethodRepository $paymentMethodRepository, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->tellerFactory = $tellerFactory;
        $this->transactionFactory = $transactionFactory;
        $this->factory = new CollectableFactory($paymentMethodRepository, $languageRelationRepository);
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        if ($this->collection){
            $tellers = $this->collection->select(fn($n) => $n->CreatedBy)->toArray();
            $approbators = $this->collection->select(fn($n) => $n->TellerFrom)->toArray();
            $ids = array_merge($tellers, $approbators);
            $this->tellerFactory->filter([['ID', 'in', $ids]]);
        }

        $this->tellerFactory->Create();
        $this->tellers = $this->tellerFactory->collectable();

        $relations = [];
        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->TransactionId)->toArray();
            $this->transactionFactory->filter([['ID', 'in', $ids]]);
        }

        $this->transactionFactory->Create();
        $this->transactions = $this->transactionFactory->collectable();

        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->PaymentId)->toArray();
            $this->factory->filter([['ID', 'in', $ids]]);
        }

        $this->factory->Create();
        $this->paymentMethods = $this->factory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $payments = [];
        if ($this->collection)
            $payments = $this->collection->select(fn($n) => new TellerPayment($n, $this->tellers, $this->transactions[$n->TransactionId], $this->paymentMethods[$n->PaymentId]))->toArray();

        $this->collectable = new TellerPayments($payments);
    }
}