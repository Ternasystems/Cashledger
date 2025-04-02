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
use Exception;
use ReflectionException;

class ContactFactory extends CollectableFactory
{
    protected ContactTypes $contactTypes;
    protected ContactRelationRepository $contactRelations;

    /**
     * @throws ReflectionException
     */
    public function __construct(ContactRepository $repository, ContactTypeRepository $_contactTypes, ContactRelationRepository $_contactRelations, LanguageRelationRepository $_relations)
    {
        parent::__construct($repository, null);
        $this->contactRelations = $_contactRelations;
        $factory = new CollectableFactory($_contactTypes, $_relations);
        $factory->Create();
        $this->contactTypes = $factory->Collectable();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item)
            $colArray[] = new Contact($item, $this->contactTypes->FirstOrDefault(fn($n) => $n->It()->Id == $item->ContactTypeId), $this->contactRelations);

        $this->collectable = new Contacts($colArray);
    }

    public function Collectable(): ?Contacts
    {
        return $this->collectable;
    }

    public function Repository(): ContactRepository
    {
        return $this->repository;
    }
}