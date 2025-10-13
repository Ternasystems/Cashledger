<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Contacts;
use API_ProfilingEntities_Collection\ContactTypes;
use API_ProfilingEntities_Model\Contact;
use API_ProfilingRepositories\ContactRepository;
use API_ProfilingRepositories\ContactTypeRepository;
use API_RelationRepositories\ContactRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Collection\ContactRelations;
use mysql_xdevapi\Collection;
use TS_Exception\Classes\DomainException;

class ContactFactory extends CollectableFactory
{
    private ContactRelationRepository $contactRelationRepository;
    private ContactRelations $contactRelations;
    private ContactTypeRepository $contactTypeRepository;
    private CollectableFactory $factory;
    private ContactTypes $contactTypes;

    public function __construct(ContactRepository $repository, ContactTypeRepository $contactTypeRepository, ContactRelationRepository $relationRepository,
                                LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->contactRelationRepository = $relationRepository;
        $this->contactTypeRepository = $contactTypeRepository;

        $this->factory = new CollectableFactory($this->contactTypeRepository, $languageRelationRepository);
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        $relations = [];
        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->Id)->toArray();
            $relations = $this->contactRelationRepository->getBy([['ContactID', 'in', $ids]]);
        }
        $this->contactRelations = new ContactRelations($relations);

        $this->factory->Create();
        $this->contactTypes = $this->factory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $contacts = [];
        if ($this->collection)
            $contacts = $this->collection->select(fn($n) => new Contact($n, $this->contactTypes[$n->ContactTypeId], $this->contactRelations))->toArray();

        $this->collectable = new Contacts($contacts);
    }
}