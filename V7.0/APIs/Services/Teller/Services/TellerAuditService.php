<?php

namespace API_Teller_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\TellerException;
use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\LanguageRelationRepository;
use API_Teller_Contract\ITellerAuditService;
use API_TellerEntities_Collection\TellerAudits;
use API_TellerEntities_Model\TellerAudit;
use API_TellerRepositories\TellerAuditRepository;
use ReflectionException;
use Throwable;
use TS_Exception\Classes\DomainException;

class TellerAuditService implements ITellerAuditService
{
    protected TellerAuditRepository $tellerAuditRepository;
    protected CollectableFactory $factory;
    protected TellerAudits $tellerAudits;
    protected LanguageRelationRepository $relationRepository;

    /**
     * @throws ReflectionException
     */
    function __construct(TellerAuditRepository $_tellerAuditRepository, LanguageRelationRepository $_relationRepository)
    {
        $this->tellerAuditRepository = $_tellerAuditRepository;
        $this->relationRepository = $_relationRepository;

        $this->factory = new CollectableFactory($this->tellerAuditRepository, $this->relationRepository);
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getTellerAudits(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): TellerAudit|TellerAudits|null
    {
        if (!isset($this->tellerAudits) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->tellerAudits = $this->factory->collectable();
        }

        if (count($this->tellerAudits) === 0)
            return null;

        return $this->tellerAudits->count() > 1 ? $this->tellerAudits : $this->tellerAudits->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws TellerException
     * @throws Throwable
     */
    public function SetTellerAudit(array $data): TellerAudit
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main tellerAudit DTO
            $tellerAudit = new \API_TellerRepositories_Model\TellerAudit($data['tellerAuditData']);
            $this->factory->repository()->add($tellerAudit);

            // 2. Get the newly created tellerAudit
            $tellerAudit = $this->factory->repository()->first([['Name', '=', $data['tellerAuditData']['Name']]]);
            if (!$tellerAudit)
                throw new TellerException('tellerAudit_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getTellerAudits([['Id', '=', $tellerAudit->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}