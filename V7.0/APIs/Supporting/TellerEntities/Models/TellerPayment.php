<?php

namespace API_TellerEntities_Model;

use API_Assets\Classes\EntityException;
use API_DTOEntities_Model\Entity;
use API_PaymentsEntities_Model\PaymentMethod;
use API_TellerEntities_Collection\Tellers;

class TellerPayment extends Entity
{
    private Teller $teller;
    private Teller $approbator;
    private TellerTransaction $transaction;
    private PaymentMethod $paymentMethod;

    /**
     * Initializes a new instance of the TellerPayment class.
     * The LanguageRelations are now handled implicitly by the base Entity class.
     *
     * @param \API_TellerRepositories_Model\TellerPayment $_entity The raw TellerPayment DTO.
     */
    public function __construct(\API_TellerRepositories_Model\TellerPayment $_entity, Tellers $_tellers, TellerTransaction $_transaction, PaymentMethod $_paymentMethod)
    {
        parent::__construct($_entity);
        $this->teller = $_tellers->first(fn($n) => $_entity->CreatedBy == $n->it()->Id);
        $this->approbator = $_tellers->first(fn($n) => $_entity->ApprovedBy == $n->it()->Id);
        $this->transaction = $_transaction;
        $this->paymentMethod = $_paymentMethod;
    }

    /**
     * @throws EntityException
     */
    public function it(): \API_TellerRepositories_Model\TellerPayment
    {
        $entity = parent::it();
        if (!$entity instanceof \API_TellerRepositories_Model\TellerPayment) {
            throw new EntityException('invalid_entity_name', [':name' => \API_TellerRepositories_Model\TellerPayment::class]);
        }

        return $entity;
    }

    public function Teller(): Teller
    {
        return $this->teller;
    }

    public function Approbator(): Teller
    {
        return $this->approbator;
    }

    public function Transaction(): TellerTransaction
    {
        return $this->transaction;
    }

    public function PaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }
}