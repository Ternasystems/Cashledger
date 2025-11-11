<?php

namespace API_Teller_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\TellerException;
use API_Teller_Contract\ITellerService;
use API_TellerEntities_Collection\Tellers;
use API_TellerEntities_Factory\TellerFactory;
use API_TellerEntities_Model\Teller;
use Throwable;
use TS_Exception\Classes\DomainException;

class TellerService implements ITellerService
{
    protected TellerFactory $tellerFactory;
    protected Tellers $tellers;

    public function __construct(TellerFactory $tellerFactory)
    {
        $this->tellerFactory = $tellerFactory;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getTellers(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Teller|Tellers|null
    {
        if (!isset($this->tellers) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->tellerFactory->filter($filter, $pageSize, $offset);
            $this->tellerFactory->Create();
            $this->tellers = $this->tellerFactory->collectable();
        }

        if (count($this->tellers) === 0)
            return null;

        return $this->tellers->count() > 1 ? $this->tellers : $this->tellers->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function setTeller(array $data): Teller
    {
        $context = $this->tellerFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main teller DTO
            $teller = new \API_TellerRepositories_Model\Teller($data['tellerData']);
            $this->tellerFactory->repository()->add($teller);

            // 2. Get the newly created teller
            $teller = $this->tellerFactory->repository()->first([['ProfileId', '=', $data['tellerData']['ProfileId']]]);
            if (!$teller)
                throw new TellerException('teller_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getTellers([['Id', '=', $teller->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws TellerException
     * @throws Throwable
     */
    public function putTeller(string $id, array $data): ?Teller
    {
        $this->tellerFactory->Create();
        $context = $this->tellerFactory->repository()->context;
        $context->beginTransaction();

        try{
            $teller = $this->getTellers([['Id', '=', $id]])?->first();
            if (!$teller)
                throw new TellerException('teller_not_found', ["Id" => $id]);

            // 1. Update the main teller record
            foreach ($data as $field => $value)
                $teller->it()->{$field} = $value ?? $teller->it()->{$field};

            $this->tellerFactory->repository()->update($teller->it());
            $context->commit();

            return $this->getTellers([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function deleteTeller(string $id): bool
    {
        $this->tellerFactory->Create();
        $context = $this->tellerFactory->repository()->context;
        $context->beginTransaction();

        try{
            $teller = $this->getTellers([['Id', '=', $id]])?->first();
            if (!$teller){
                $context->commit();
                return true;
            }

            $this->tellerFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function disableTeller(string $id): bool
    {
        $this->tellerFactory->Create();
        $context = $this->tellerFactory->repository()->context;
        $context->beginTransaction();

        try{
            $teller = $this->getTellers([['Id', '=', $id]])?->first();
            if (!$teller){
                $context->commit();
                return true;
            }

            $this->tellerFactory->repository()->deactivate($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}