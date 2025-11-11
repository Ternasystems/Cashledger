<?php

namespace API_HrmEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_HrmEntities_Collection\Employees;
use API_HrmEntities_Model\Employee;
use API_HrmRepositories\EmployeeRepository;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Factory\ProfileFactory;
use API_RelationRepositories\LanguageRelationRepository;
use TS_Exception\Classes\DomainException;

class EmployeeFactory extends CollectableFactory
{
    private ProfileFactory $profileFactory;
    private Profiles $profiles;

    public function __construct(EmployeeRepository $repository, ProfileFactory $profileFactory, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->profileFactory = $profileFactory;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->ProfileId)->toArray();
            $this->profileFactory->filter([['ProfileID', 'in', $ids]]);
        }

        $this->profileFactory->Create();
        $this->profiles = $this->profileFactory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $employees = [];
        if ($this->collection)
            $employees = $this->collection->select(fn($n) => new Employee($n, $this->profiles[$n->ProfileId]))->toArray();

        $this->collectable = new Employees($employees);
    }
}