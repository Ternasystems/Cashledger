<?php

namespace API_Teller_Contract;

use API_Administration_Service\ReloadMode;
use API_TellerEntities_Collection\TellerPayments;
use API_TellerEntities_Model\TellerPayment;

interface ITellerPaymentService
{
    /**
     * Gets a paginated list of TellerPayment entities.
     *
     * @param array|null $filter
     * @param int $page
     * @param int $pageSize
     * @param ReloadMode $reloadMode
     * @return TellerPayment|TellerPayments|null An associative array containing 'data' and 'total'.
     */
    public function getTellerPayments(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerPayment|TellerPayments|null;

    /**
     * Creates a new TellerPayment and assigns roles.
     *
     * @param array $data
     * @return TellerPayment The newly created TellerPayment entity.
     */
    public function SetTellerPayment(array $data): TellerPayment;
}