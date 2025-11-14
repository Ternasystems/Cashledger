<?php

namespace API_Administration_Service;

use API_Administration_Contract\IAuditService;
use API_DTOEntities_Collection\Audits;
use API_DTOEntities_Factory\CollectableFactory;
use API_DTOEntities_Model\Audit;
use API_DTORepositories\AuditRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use TS_Exception\Classes\DomainException;

class AuditService implements IAuditService
{
    protected CollectableFactory $factory;
    protected Audits $audits;

    /**
     * @throws ReflectionException
     */
    public function __construct(AuditRepository $categoryRepository, LanguageRelationRepository $relationRepository)
    {
        $this->factory = new CollectableFactory($categoryRepository, $relationRepository);
    }
    
    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getAudits(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Audit|Audits|null
    {
        if (!isset($this->audits) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->audits = $this->factory->collectable();
        }

        if (count($this->audits) === 0)
            return null;

        return $this->audits->count() > 1 ? $this->audits : $this->audits->first();
    }
}