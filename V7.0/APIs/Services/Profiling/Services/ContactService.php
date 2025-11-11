<?php

namespace API_Profiling_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\DTOException;
use API_Assets\Classes\ProfilingException;
use API_Profiling_Contract\IContactService;
use API_ProfilingEntities_Collection\Contacts;
use API_ProfilingEntities_Factory\ContactFactory;
use API_ProfilingEntities_Model\Contact;
use API_RelationRepositories\ContactRelationRepository;
use API_RelationRepositories_Model\ContactRelation;
use Exception;
use Throwable;
use TS_Exception\Classes\DomainException;

class ContactService implements IContactService
{
    protected ContactFactory $contactFactory;
    protected Contacts $contacts;
    protected ContactRelationRepository $contactRelationRepository;

    public function __construct(ContactFactory $contactFactory, ContactRelationRepository $contactRelationRepository)
    {
        $this->contactFactory = $contactFactory;
        $this->contactRelationRepository = $contactRelationRepository;
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
     * @throws Exception
     * @throws DomainException
     * @throws Throwable
     */
    public function setContact(array $data): Contact
    {
        $context = $this->contactFactory->repository()->context;
        $context->beginTransaction();

        try {
            // Create a new Contact object
            $contact = new \API_ProfilingRepositories_Model\Contact($data['contactData']);

            // Insert the new Contact object in the database and retrieve it
            $this->contactFactory->repository()->add($contact);
            $contact = $this->contactFactory->repository()->first([['ProfileId', '=', $data['profileId']], ['Name', '=', $data['name']]]);
            if (!$contact)
                throw new ProfilingException('contact_creation_failed');

            // Create and insert related ContactRelations
            if (isset($data['contactRelations'])){
                foreach ($data['contactRelations'] as $contactRelation) {
                    $contactRelation['ContactId'] = $contact->Id;
                    $relation = new ContactRelation($contactRelation);
                    $this->contactRelationRepository->add($relation);
                }
            }

            // If all operations succeeded, commit the transaction.
            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getContacts([['Id', '=', $contact->Id]], 1, 1, ReloadMode::YES);
        } catch (Throwable $e) {
            $context->rollback();
            throw $e;
        }
    }

    /**
     * @throws DomainException
     * @throws DTOException
     * @throws Throwable
     */
    public function putContact(string $id, array $data): ?Contact
    {
        $context = $this->contactFactory->repository()->context;
        $context->beginTransaction();

        try {
            // Fetch the Contact item
            $contact = $this->getContacts([['Id', '=', $id]])?->first();
            if (!$contact)
                throw new DomainException('contact_not_found');

            // 1. Update the main civility record
            foreach ($data as $field => $value)
                $contact->it()->{$field} = $value ?? $contact->it()->{$field};

            $this->contactFactory->repository()->update($contact->it());

            // Delete the contact relations
            if ($contact->contactRelations()){
                $contactRelations = $contact->ContactRelations();
                foreach ($contactRelations as $relation)
                    $this->contactRelationRepository->remove($relation->Id);
            }

            // Update the contact relations
            if ($data['contactRelations']){
                foreach ($data['contactRelations'] as $contactRelation) {
                    $contactRelation['ContactId'] = $id;
                    $relation = new ContactRelation($contactRelation);
                    $this->contactRelationRepository->add($relation);
                }
            }

            // If all operations succeeded, commit the transaction.
            $context->commit();

            return $this->getContacts([['Id', '=', $contact->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e) {
            $context->rollback();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function deleteContact(string $id): bool
    {
        $context = $this->contactFactory->repository()->context;
        $context->beginTransaction();

        try {
            // Retrieve the contact entity
            $contact = $this->getContacts([['Id', '=', $id]])?->first();
            if (!$contact){
                $context->commit();
                return true;
            }

            // Deactivate the contact relations
            if ($contact->ContactRelations()) {
                $contactRelations = $contact->ContactRelations();
                foreach ($contactRelations as $relation)
                    $this->contactRelationRepository->remove($relation->Id);
            }

            // Deactivate the contact
            $this->contactFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}