<?php

namespace API_Teller_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\TellerException;
use API_Teller_Contract\ITellerSessionService;
use API_TellerEntities_Collection\TellerSessions;
use API_TellerEntities_Factory\TellerSessionFactory;
use API_TellerEntities_Model\TellerSession;
use Throwable;
use TS_Exception\Classes\DomainException;

class TellerSessionService implements ITellerSessionService
{
    protected TellerSessionFactory $sessionFactory;
    protected TellerSessions $sessions;

    public function __construct(TellerSessionFactory $sessionFactory)
    {
        $this->sessionFactory = $sessionFactory;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getTellerSessions(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerSession|TellerSessions|null
    {
        if (!isset($this->sessions) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->sessionFactory->filter($filter, $pageSize, $offset);
            $this->sessionFactory->Create();
            $this->sessions = $this->sessionFactory->collectable();
        }

        if (count($this->sessions) === 0)
            return null;

        return $this->sessions->count() > 1 ? $this->sessions : $this->sessions->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function SetTellerSession(array $data): TellerSession
    {
        $context = $this->sessionFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main session DTO
            $session = new \API_TellerRepositories_Model\TellerSession($data['sessionData']);
            $this->sessionFactory->repository()->add($session);

            // 2. Get the newly created session
            $session = $this->sessionFactory->repository()->first([['TellerId', '=', $data['sessionData']['TellerId']], ['SessionId', '=', $data['sessionData']['SessionId']]]);
            if (!$session)
                throw new TellerException('session_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getTellerSessions([['Id', '=', $session->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}