<?php

namespace API_Profiling_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\ProfilingException;
use API_DTOEntities_Factory\CollectableFactory;
use API_Profiling_Contract\IContactTypeService;
use API_ProfilingEntities_Collection\ContactTypes;
use API_ProfilingEntities_Model\ContactType;
use API_ProfilingRepositories\ContactTypeRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use Throwable;
use TS_Exception\Classes\DomainException;

class ContactTypeService implements IContactTypeService
{
    protected ContactTypeRepository $contactTypeRepository;
    protected CollectableFactory $factory;
    protected ContactTypes $contactTypes;

    /**
     * @throws ReflectionException
     */
    public function __construct(ContactTypeRepository $contactTypeRepository, LanguageRelationRepository $languageRelationRepository)
    {
        $this->contactTypeRepository = $contactTypeRepository;

        // Use the generic factory for the simple ContactType entity.
        $this->factory = new CollectableFactory($contactTypeRepository, $languageRelationRepository);
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
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function setContactType(array $data): ContactType
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main contactType DTO
            $contactType = new \API_ProfilingRepositories_Model\ContactType($data['contactTypeData']);
            $this->factory->repository()->add($contactType);

            // 2. Get the newly created contactType
            $contactType = $this->factory->repository()->first([['Name', '=', $data['contactTypeData']['Name']]]);
            if (!$contactType)
                throw new ProfilingException('contactType_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getContactTypes([['Id', '=', $contactType->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function putContactType(string $id, array $data): ?ContactType
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $contactType = $this->getContactTypes([['Id', '=', $id]])?->first();
            if (!$contactType)
                throw new ProfilingException('entity_not_found', ["Id" => $id]);

            // 1. Update the main contactType record
            foreach ($data as $field => $value)
                $contactType->it()->{$field} = $value ?? $contactType->it()->{$field};

            $this->factory->repository()->update($contactType->it());
            $context->commit();

            return $this->getContactTypes([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function deleteContactType(string $id): bool
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $appCategory = $this->getContactTypes([['Id', '=', $id]])?->first();
            if (!$appCategory){
                $context->commit();
                return true;
            }

            $this->factory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}