<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Credentials;
use API_ProfilingEntities_Collection\Trackings;
use API_ProfilingEntities_Model\Tracking;
use API_ProfilingRepositories\TrackingRepository;
use Exception;

class TrackingFactory extends CollectableFactory
{
    protected Credentials $credentials;

    /**
     * @throws Exception
     */
    public function __construct(TrackingRepository $repository, CredentialFactory $credentialFactory)
    {
        parent::__construct($repository, null);
        $credentialFactory->Create();
        $this->credentials = $credentialFactory->Collectable();
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item) {
            $credential = $this->credentials->FirstOrDefault(fn($n) => $n->It()->Id == $item->CredentialId);
            $colArray[] = new Tracking($item, $credential);
        }

        $this->collectable = new Trackings($colArray);
    }

    public function Collectable(): ?Trackings
    {
        return $this->collectable;
    }

    public function Repository(): TrackingRepository
    {
        return $this->repository;
    }
}