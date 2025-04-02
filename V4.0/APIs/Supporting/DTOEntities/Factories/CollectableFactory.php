<?php

namespace API_DTOEntities_Factory;

use API_DTOEntities_Collection\EntityCollectable;
use API_DTOEntities_Contract\ICollectableFactory;
use API_DTORepositories_Contract\IRepository;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use API_RelationRepositories\LanguageRelationRepository;
use TS_Utility\Classes\AbstractCollectable;

class CollectableFactory implements ICollectableFactory
{
    private array $modelMap = [
        'Audit' => ['API_DTOEntities_Model\Audit', 'API_DTOEntities_Collection\Audits'],
        'City' => ['API_DTOEntities_Model\City', 'API_DTOEntities_Collection\Cities'],
        'Continent' => ['API_DTOEntities_Model\Continent', 'API_DTOEntities_Collection\Continents'],
        'Country' => ['API_DTOEntities_Model\Country', 'API_DTOEntities_Collection\Countries'],
        'ContactType' => ['API_ProfilingEntities_Model\ContactType', 'API_ProfilingEntities_Collection\ContactTypes'],
        'Language' => ['API_DTOEntities_Model\Language', 'API_DTOEntities_Collection\Languages'],
        'Manufacturer' => ['API_Inventories_Model\Manufacturer', 'API_Inventories_Collection\Manufacturers'],
        'Packaging' => ['API_Inventories_Model\Packaging', 'API_Inventories_Collection\Packagings'],
        'Permission' => ['API_ProfilingEntities_Model\Permission', 'API_ProfilingEntities_Collection\Permissions'],
        'ProductCategory' => ['API_Inventories_Model\ProductCategory', 'API_Inventories_Collection\ProductCategories'],
        'Unit' => ['API_Inventories_Model\Unit', 'API_Inventories_Collection\Units'],
        'Warehouse' => ['API_Inventories_Model\Warehouse', 'API_Inventories_Collection\Warehouses']
    ];
    protected IRepository $repository;
    protected ?LanguageRelationRepository $relationRepository;
    protected EntityCollectable|AbstractCollectable|null $collectable;

    public function __construct(IRepository $_repository, ?LanguageRelationRepository $_relationRepository)
    {
        $this->repository = $_repository;
        $this->relationRepository = $_relationRepository;
    }

    /**
     * @throws ReflectionException
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        if (is_null($collection)){
            $this->collectable = null;
            return;
        }

        $className = get_class($collection[0]);
        $className = explode('\\', $className)[1];

        if (!isset($this->modelMap[$className]))
            throw new InvalidArgumentException('No Entity mapping found for model ' . $className);

        $entityClass = new ReflectionClass($this->modelMap[$className][0]);
        $entityArray = [];
        foreach ($collection as $item)
            $entityArray[] = $entityClass->newInstance($item, $this->relationRepository->GetAll());

        $collectionClass = new ReflectionClass($this->modelMap[$className][1]);
        $this->collectable = $collectionClass->newInstance($entityArray);
    }

    public function ToArray(): ?array
    {
        return $this->collectable?->toArray();
    }

    public function Collectable(): EntityCollectable|AbstractCollectable|null
    {
        return $this->collectable;
    }

    public function Repository(): IRepository
    {
        return $this->repository;
    }

    /**
     * @throws ReflectionException
     */
    public function Reset(): void
    {
        $this->collectable = null;
        $this->Create();
    }
}