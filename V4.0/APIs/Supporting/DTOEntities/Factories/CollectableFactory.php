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
        'AppCategory' => ['API_DTOEntities_Model\AppCategory', 'API_DTOEntities_Collection\AppCategories'],
        'Audit' => ['API_DTOEntities_Model\Audit', 'API_DTOEntities_Collection\Audits'],
        'Continent' => ['API_DTOEntities_Model\Continent', 'API_DTOEntities_Collection\Continents'],
        'ContactType' => ['API_ProfilingEntities_Model\ContactType', 'API_ProfilingEntities_Collection\ContactTypes'],
        'DeliveryNote' => ['API_InventoryEntities_Model\DeliveryNote', 'API_InventoryEntities_Collection\DeliveryNotes'],
        'DispatchNote' => ['API_InventoryEntities_Model\DispatchNote', 'API_InventoryEntities_Collection\DispatchNotes'],
        'Language' => ['API_DTOEntities_Model\Language', 'API_DTOEntities_Collection\Languages'],
        'Manufacturer' => ['API_InventoryEntities_Model\Manufacturer', 'API_InventoryEntities_Collection\Manufacturers'],
        'Packaging' => ['API_InventoryEntities_Model\Packaging', 'API_InventoryEntities_Collection\Packagings'],
        'Permission' => ['API_ProfilingEntities_Model\Permission', 'API_ProfilingEntities_Collection\Permissions'],
        'ProductCategory' => ['API_InventoryEntities_Model\ProductCategory', 'API_InventoryEntities_Collection\ProductCategories'],
        'Unit' => ['API_InventoryEntities_Model\Unit', 'API_InventoryEntities_Collection\Units'],
        'Warehouse' => ['API_InventoryEntities_Model\Warehouse', 'API_InventoryEntities_Collection\Warehouses']
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
            $entityArray[] = $entityClass->newInstance($item, $this->relationRepository?->GetAll());

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