<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Credentials;
use API_ProfilingEntities_Collection\Trackings;
use API_ProfilingEntities_Model\Tracking;
use API_ProfilingRepositories\TrackingRepository;
use API_RelationRepositories\LanguageRelationRepository;
use TS_Exception\Classes\DomainException;

class TrackingFactory extends CollectableFactory
{
    private CredentialFactory $factory;
    private Credentials $credentials;

    public function __construct(TrackingRepository $repository, CredentialFactory $factory, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->factory = $factory;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository()->getBy($this->whereClause, $this->limit, $this->offset);

        $this->factory->Create();
        $this->credentials = $this->factory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $trackings = [];
        if ($this->collection)
            $trackings = $this->collection->select(fn($n) => new Tracking($n, $this->credentials[$n->CredentialId]))->toArray();

        $this->collectable = new Trackings($trackings);
    }
}