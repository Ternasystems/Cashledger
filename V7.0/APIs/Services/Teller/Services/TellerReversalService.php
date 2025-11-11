<?php

namespace API_Teller_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\TellerException;
use API_Teller_Contract\ITellerReversalService;
use API_TellerEntities_Collection\TellerReversals;
use API_TellerEntities_Factory\TellerReversalFactory;
use API_TellerEntities_Model\TellerReversal;
use Throwable;
use TS_Exception\Classes\DomainException;

class TellerReversalService implements ITellerReversalService
{
    protected TellerReversalFactory $reversalFactory;
    protected TellerReversals $reversals;

    public function __construct(TellerReversalFactory $reversalFactory)
    {
        $this->reversalFactory = $reversalFactory;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getTellerReversals(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerReversal|TellerReversals|null
    {
        if (!isset($this->reversals) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->reversalFactory->filter($filter, $pageSize, $offset);
            $this->reversalFactory->Create();
            $this->reversals = $this->reversalFactory->collectable();
        }

        if (count($this->reversals) === 0)
            return null;

        return $this->reversals->count() > 1 ? $this->reversals : $this->reversals->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function SetTellerReversal(array $data): TellerReversal
    {
        $context = $this->reversalFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main reversal DTO
            $reversal = new \API_TellerRepositories_Model\TellerReversal($data['reversalData']);
            $this->reversalFactory->repository()->add($reversal);

            // 2. Get the newly created reversal
            $reversal = $this->reversalFactory->repository()->first([['TransactionId', '=', $data['reversalData']['TransactionId']]]);
            if (!$reversal)
                throw new TellerException('reversal_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getTellerReversals([['Id', '=', $reversal->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}