<?php

namespace API_PaymentsRepositories;

use API_DTORepositories\Repository;
use API_PaymentsRepositories_Context\PaymentsContext;
use API_PaymentsRepositories_Model\PaymentMethod;

/**
 * @extends Repository<PaymentMethod>
 */
class PaymentMethodRepository extends Repository
{
    public function __construct(PaymentsContext $context)
    {
        parent::__construct($context, PaymentMethod::class);
    }
}