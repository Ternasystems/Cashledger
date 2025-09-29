<?php

namespace API_Administration_Service;

use API_Administration_Contract\IAppService;
use API_DTOEntities_Collection\AppCategories;
use API_DTOEntities_Collection\Apps;
use API_DTOEntities_Factory\AppFactory;
use API_DTOEntities_Factory\CollectableFactory;
use API_DTOEntities_Model\App;
use API_DTOEntities_Model\AppCategory;
use API_DTORepositories\AppCategoryRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use TS_Exception\Classes\DomainException;

class AppService implements IAppService
{
    protected AppFactory $appFactory;
    protected Apps $apps;
    protected AppCategoryRepository $categoryRepository;
    protected CollectableFactory $factory;
    protected AppCategories $appCategories;
    protected LanguageRelationRepository $relationRepository;

    public function __construct(AppFactory $_appFactory, AppCategoryRepository $_categoryRepository, LanguageRelationRepository $_relationRepository)
    {
        $this->appFactory = $_appFactory;
        $this->categoryRepository = $_categoryRepository;
        $this->relationRepository = $_relationRepository;
    }

    /**
     * @throws DomainException
     */
    public function GetApps(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): App|Apps|null
    {
        if (!isset($this->apps) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->appFactory->filter($filter, $pageSize, $offset);
            $this->appFactory->Create();
            $this->apps = $this->appFactory->collectable();
        }

        if (count($this->apps) === 0)
            return null;

        return $this->apps->count() > 1 ? $this->apps : $this->apps->first();
    }

    /**
     * @throws DomainException
     * @throws ReflectionException
     */
    public function GetCategories(?array $filter = null, ?int $page = 1, ?int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): AppCategory|AppCategories|null
    {
        if (!isset($this->appCategories) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory = new CollectableFactory($this->categoryRepository, $this->relationRepository);
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->appCategories = $this->factory->collectable();
        }

        if (count($this->appCategories) === 0)
            return null;

        return $this->appCategories->count() > 1 ? $this->appCategories : $this->appCategories->first();
    }
}