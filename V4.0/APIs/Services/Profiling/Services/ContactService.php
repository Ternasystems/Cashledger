<?php

namespace API_Profiling_Service;

use API_DTOEntities_Factory\CollectableFactory;
use API_Profiling_Contract\IContactService;
use API_ProfilingEntities_Collection\Contacts;
use API_ProfilingEntities_Collection\ContactTypes;
use API_ProfilingEntities_Factory\ContactFactory;
use API_ProfilingEntities_Model\Contact;
use API_ProfilingEntities_Model\ContactType;
use API_ProfilingRepositories\ContactTypeRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;

class ContactService implements IContactService
{
    protected Contacts $contacts;
    protected ContactTypes $contactTypes;

    /**
     * @throws ReflectionException
     */
    public function __construct(ContactFactory $contactFactory, ContactTypeRepository $repository, LanguageRelationRepository $relationRepository)
    {
        $contactFactory->Create();
        $this->contacts = $contactFactory->Collectable();

        $factory = new CollectableFactory($repository, $relationRepository);
        $factory->Create();
        $this->contactTypes = $factory->Collectable();
    }
    public function GetContacts(callable $predicate = null): Contact|Contacts|null
    {
        if (is_null($predicate))
            return $this->contacts;

        $collection = $this->contacts->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    public function GetContactTypes(callable $predicate = null): ContactType|ContactTypes|null
    {
        if (is_null($predicate))
            return $this->contactTypes;

        $collection = $this->contactTypes->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }
}