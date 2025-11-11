<?php

namespace API_Administration_Service;

use API_Administration_Contract\IAppCategoryService;
use API_Assets\Classes\AdministrationException;
use API_DTOEntities_Collection\AppCategories;
use API_DTOEntities_Factory\CollectableFactory;
use API_DTOEntities_Model\AppCategory;
use API_DTORepositories\AppCategoryRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use Throwable;
use TS_Exception\Classes\DomainException;

class AppCategoryService implements IAppCategoryService
{
    protected CollectableFactory $factory;
    protected AppCategories $appCategories;

    /**
     * @throws ReflectionException
     */
    public function __construct(AppCategoryRepository $categoryRepository, LanguageRelationRepository $relationRepository)
    {
        $this->factory = new CollectableFactory($categoryRepository, $relationRepository);
    }

    /**
     * @throws DomainException
     */
    public function GetAppCategories(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): AppCategory|AppCategories|null
    {
        if (!isset($this->appCategories) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->appCategories = $this->factory->collectable();
        }

        if (count($this->appCategories) === 0)
            return null;

        return $this->appCategories->count() > 1 ? $this->appCategories : $this->appCategories->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function setAppCategory(array $data): AppCategory
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main appCategory DTO
            $appCategory = new \API_DTORepositories_Model\AppCategory($data['appCategoryData']);
            $this->factory->repository()->add($appCategory);

            // 2. Get the newly created appCategory
            $appCategory = $this->factory->repository()->first([['Name', '=', $data['appCategoryData']['Name']]]);
            if (!$appCategory)
                throw new AdministrationException('appCategory_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getAppCategories([['Id', '=', $appCategory->Id]], 1, 1, ReloadMode::YES);

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
    public function putAppCategory(string $id, array $data): ?AppCategory
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $appCategory = $this->getAppCategories([['Id', '=', $id]])?->first();
            if (!$appCategory)
                throw new AdministrationException('entity_not_found', ["Id" => $id]);

            // 1. Update the main appCategory record
            foreach ($data as $field => $value)
                $appCategory->it()->{$field} = $value ?? $appCategory->it()->{$field};

            $this->factory->repository()->update($appCategory->it());
            $context->commit();

            return $this->getAppCategories([['Id', '=', $id]], 1, 1, ReloadMode::YES);

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
    public function deleteAppCategory(string $id): bool
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $appCategory = $this->getAppCategories([['Id', '=', $id]])?->first();
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