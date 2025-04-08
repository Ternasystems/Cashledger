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
use Exception;

class AppService implements IAppService
{
    protected Apps $apps;
    protected AppCategories $appCategories;

    /**
     * @throws Exception
     */
    public function __construct(AppFactory $appFactory, AppCategoryRepository $repository, LanguageRelationRepository $relationRepository)
    {
        $appFactory->Create();
        $this->apps = $appFactory->Collectable();

        $factory = new CollectableFactory($repository, $relationRepository);
        $factory->Create();
        $this->appCategories = $factory->Collectable();
    }

    public function GetApps(callable $predicate = null): App|Apps|null
    {
        if (is_null($predicate))
            return $this->apps;

        $collection = $this->apps->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    public function GetCategories(callable $predicate = null): AppCategory|AppCategories|null
    {
        if (is_null($predicate))
            return $this->appCategories;

        $collection = $this->appCategories->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }
}