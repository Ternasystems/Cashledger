<?php

namespace API_Teller_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\TellerException;
use API_RelationRepositories\CashRelationRepository;
use API_RelationRepositories_Model\CashRelation;
use API_Teller_Contract\ITellerCashCountService;
use API_TellerEntities_Collection\TellerCashCounts;
use API_TellerEntities_Model\TellerCashCount;
use API_TellerEntities_Factory\TellerCashCountFactory;
use Throwable;
use TS_Exception\Classes\DomainException;

class TellerCashCountService implements ITellerCashCountService
{
    protected TellerCashCountFactory $cashCountFactory;
    protected TellerCashCounts $cashCounts;
    protected CashRelationRepository $cashRelationRepository;

    public function __construct(TellerCashCountFactory $_cashCountFactory, CashRelationRepository $_cashRelationRepository)
    {
        $this->cashCountFactory = $_cashCountFactory;
        $this->cashRelationRepository = $_cashRelationRepository;
    }
    
    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getTellerCashCounts(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerCashCount|TellerCashCounts|null
    {
        if (!isset($this->cashCounts) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->cashCountFactory->filter($filter, $pageSize, $offset);
            $this->cashCountFactory->Create();
            $this->cashCounts = $this->cashCountFactory->collectable();
        }

        if (count($this->cashCounts) === 0)
            return null;

        return $this->cashCounts->count() > 1 ? $this->cashCounts : $this->cashCounts->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function SetTellerCashCount(array $data): TellerCashCount
    {
        $context = $this->cashCountFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main cashCount DTO
            $cashCount = new \API_TellerRepositories_Model\TellerCashCount($data['cashCountData']);
            $this->cashCountFactory->repository()->add($cashCount);

            // 2. Get the newly created cashCount
            $cashCount = $this->cashCountFactory->repository()->first([['TellerID', '=', $data['cashCountData']['TellerID']], ['CountDate', '=', $data['cashCountData']['CountDate']]]);
            if (!$cashCount)
                throw new TellerException('cashcount_creation_failed');

            if (isset($data['cashRelations'])){
                foreach ($data['cashRelations'] as $cashRelation){
                    $cashRelation['CountId'] = $cashCount->Id;
                    $relation = new CashRelation($cashRelation);
                    $this->cashRelationRepository->add($relation);
                }
            }

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getTellerCashCounts([['Id', '=', $cashCount->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}