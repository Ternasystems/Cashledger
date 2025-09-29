<?php

namespace API_Profiling_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\DTOException;
use API_DTOEntities_Factory\CollectableFactory;
use API_Profiling_Contract\IContactService;
use API_ProfilingEntities_Collection\Contacts;
use API_ProfilingEntities_Collection\ContactTypes;
use API_ProfilingEntities_Factory\ContactFactory;
use API_ProfilingEntities_Model\Contact;
use API_ProfilingEntities_Model\ContactType;
use API_ProfilingRepositories\ContactTypeRepository;
use API_RelationRepositories\ContactRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Collection\ContactRelations;
use API_RelationRepositories_Model\ContactRelation;
use Exception;
use TS_Exception\Classes\DomainException;

class ContactService implements IContactService
{
    protected ContactFactory $contactFactory;
    protected Contacts $contacts;
    protected CollectableFactory $factory;
    protected ContactTypes $contactTypes;
    protected ContactRelationRepository $contactRelationRepository;
    protected ContactRelations $contactRelations;

    /**
     * @throws \ReflectionException
     */
    public function __construct(ContactFactory $contactFactory, ContactTypeRepository $contactTypeRepository, ContactRelationRepository $contactRelationRepository,
                                LanguageRelationRepository $languageRelationRepository)
    {
        $this->contactFactory = $contactFactory;
        $this->contactRelationRepository = $contactRelationRepository;

        // Use the generic factory for the simple ContactType entity.
        $this->factory = new CollectableFactory($contactTypeRepository, $languageRelationRepository);
    }

    /**
     * @throws DomainException
     */
    public function getContacts(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Contact|Contacts|null
    {
        if (!isset($this->contacts) || $reloadMode === ReloadMode::YES) {
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->contactFactory->filter($filter, $pageSize, $offset);
            $this->contactFactory->Create();
            $this->contacts = $this->contactFactory->collectable();
        }

        if (count($this->contacts) === 0)
            return null;

        return $this->contacts->count() > 1 ? $this->contacts : $this->contacts->first();
    }

    /**
     * @throws DomainException
     */
    public function getContactTypes(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): ContactType|ContactTypes|null
    {
        if (!isset($this->contactTypes) || $reloadMode === ReloadMode::YES) {
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->contactTypes = $this->factory->collectable();
        }

        if (count($this->contactTypes) === 0)
            return null;

        return $this->contactTypes->count() > 1 ? $this->contactTypes : $this->contactTypes->first();
    }

    /**
     * @throws Exception
     * @throws DomainException
     * @throws DTOException
     */
    public function SetContact(array $data): Contact
    {
        // Create a new Contact object
        $contact = new \API_ProfilingRepositories_Model\Contact([
            'ContactTypeId' => $data['ContactTypeId'],
            'ProfileId' => $data['ProfileId'],
            'Name' => $data['Name'],
            'Description' => $data['Description'] ?? null
        ]);

        // Insert the new Contact object in the database and retrieve it
        $this->contactFactory->repository()->add($contact);
        $contact = $this->contactFactory->repository()->first([['ProfileId', '=', $data['profileId']], ['Name', '=', $data['name']]]);

        // Create and insert related ContactRelations
        if ($contact && isset($data['ContactRelations'])){
            foreach ($data['ContactRelations'] as $ContactRelation) {
                $relation = new ContactRelation([
                    'LangId' => $ContactRelation['LangId'],
                    'ContactId' => $contact->Id,
                    'Contact' => $ContactRelation['Contact'],
                    'Photo' => $ContactRelation['Photo'],
                    'Description' => $ContactRelation['Description'] ?? null
                ]);
                $this->contactRelationRepository->add($relation);
            }
        }

        // Re-build the contact factory
        $this->contactFactory->filter([['ID', '=', $contact->Id]]);
        $this->contactFactory->Create();

        return $this->contactFactory->collectable()->first();
    }

    /**
     * @throws DomainException
     * @throws DTOException
     */
    public function PutContact(string $id, array $data): ?Contact
    {
        // Fetch the Contact item
        $contact = $this->contactFactory->collectable()->first(fn($n) => $n->it()->Id == $id);
        if (!$contact)
            throw new DomainException('contact_not_found');

        // Update the contact item
        $contact->it()->ContactTypeId = $data['ContactTypeId'] ?? $contact->it()->ContactTypeId;
        $contact->it()->ProfileId = $data['ProfileId'] ?? $contact->it()->ProfileId;
        $contact->it()->ContactNo = $data['ContactNo'] ?? $contact->it()->ContactNo;
        $contact->it()->Name = $data['Name'] ?? $contact->it()->Name;
        $contact->it()->Description = $data['Description'] ?? null;
        $this->contactFactory->repository()->update($contact->it());

        // Delete the contact relations
        if ($contact->ContactRelations()){
            $contactRelations = $contact->ContactRelations();
            foreach ($contactRelations as $relation)
                $this->contactRelationRepository->remove($relation->Id);
        }

        // Update the contact relations
        if ($data['ContactRelations']){
            foreach ($data['ContactRelations'] as $ContactRelation) {
                $relation = new ContactRelation([
                    'LangId' => $ContactRelation['LangId'],
                    'ContactId' => $contact->Id,
                    'Contact' => $ContactRelation['Contact'],
                    'Photo' => $ContactRelation['Photo'],
                    'Description' => $ContactRelation['Description'] ?? null
                ]);
                $this->contactRelationRepository->add($relation);
            }
        }

        // Re-build the contact factory
        $this->contactFactory->filter([['ID', '=', $contact->it()->Id]]);
        $this->contactFactory->Create();
        return $this->contactFactory->collectable()->first();
    }

    public function DeleteContact(string $id): bool
    {
        // Retrieve the contact entity
        $contact = $this->contactFactory->collectable()->first(fn($n) => $n->Id == $id);
        if (!$contact)
            return true;

        try {
            // Deactivate the contact relations
            if ($contact->ContactRelations()) {
                $contactRelations = $contact->ContactRelations();
                foreach ($contactRelations as $relation)
                    $this->contactRelationRepository->deactivate($relation->Id);
            }

            // Deactivate the contact
            $this->contactFactory->repository()->remove($contact->it()->Id);
            return true;
        }catch (DomainException $e){
            return false;
        }
    }
}