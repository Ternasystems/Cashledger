<?php

namespace API_DTORepositories;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Contract\IRepository;
use API_DTORepositories_Model\DTOBase;
use Exception;
use ReflectionException;
use ReflectionMethod;
use TS_Database\Classes\DBContext;
use TS_Utility\Enums\OrderEnum;

abstract class Repository implements IRepository
{
    protected readonly DBContext $context;

    public function __construct(DBContext $_context){
        $this->context = $_context;
    }

    /**
     * @throws ReflectionException
     */
    protected function GetEntityName(string $_methodName): string{
        $method = new ReflectionMethod(get_class($this), $_methodName);
        return $method->getReturnType()->getName();
    }


    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function FirstOrDefault(?callable $predicate = null): ?DTOBase
    {
        $collection = $this->GetAll();

        if (!empty($collection) && !is_null($predicate)) {
            foreach ($collection as $item) {
                if ($predicate($item))
                    return $item;
            }
        }

        return empty($collection) ? null : $collection[0];
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function GetAll(): ?Collectable
    {
        $entityName = $this->GetEntityName('GetById');
        $entityName = strtolower(explode('\\', $entityName)[1]);
        $data = $this->context->SelectAll($entityName);

        if (empty($data))
            return null;

        $objectArray = [];
        foreach ($data as $item)
            $objectArray[] = $this->context->Mapping($entityName, $item);

        return $this->context->MappingCollection($entityName.'collection', $objectArray);
    }

    /**
     * @throws Exception
     */
    public function GetById(string $id): ?DTOBase
    {
        $entityName = $this->GetEntityName('GetById');
        $entityName = strtolower(explode('\\', $entityName)[1]);
        $data = $this->context->SelectById($id, $entityName);

        if (empty($data))
            return null;

        return $this->context->Mapping($entityName, $data[0]);
    }

    /**
     * @throws ReflectionException
     */
    public function GetBy(callable $predicate): ?Collectable
    {
        $collection = $this->GetAll();
        if (empty($collection))
            return null;

        $objectArray = [];
        foreach ($collection as $item){
            if ($predicate($item))
                $objectArray[] = $item;
        }

        $entityName = $this->GetEntityName('GetById');
        $entityName = strtolower(explode('\\', $entityName)[1]);
        return empty($objectArray) ? null : $this->context->MappingCollection($entityName.'collection', $objectArray);
    }

    /**
     * @throws ReflectionException
     */
    public function LastOrDefault(?callable $predicate = null): ?DTOBase
    {
        $collection = $this->GetAll();

        if (!empty($collection) && !is_null($predicate)) {
            $objectArray = [];
            foreach ($collection as $item) {
                if ($predicate($item))
                    $objectArray[] = $item;
            }
            return !empty($objectArray) ? $objectArray[count($objectArray) - 1] : $collection[$collection->count() - 1];
        }

        return empty($collection) ? null : $collection[$collection->count() - 1];
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function Add(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $entity = $this->GetEntityName('GetById');
        if ($entityName !== $entity)
            throw new Exception('Expected '.$entity.' but got '.$entityName);

        $this->context->Insert($entity, $args);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function Remove(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $entity = $this->GetEntityName('GetById');
        if ($entityName !== $entity)
            throw new Exception('Expected '.$entity.' but got '.$entityName);

        $this->context->Delete($entity, $args);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function Deactivate(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $entity = $this->GetEntityName('GetById');
        if ($entityName !== $entity)
            throw new Exception('Expected '.$entity.' but got '.$entityName);

        $this->context->Disable($entity, $args);
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function Update(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $entity = $this->GetEntityName('GetById');
        if ($entityName !== $entity)
            throw new Exception('Expected '.$entity.' but got '.$entityName);

        $this->context->Update($entity, $args);
    }

    /**
     * @throws Exception
     */
    public function OrderBy(Collectable $collection, array $properties, array $orderBy = [OrderEnum::ASC]): ?Collectable
    {
        if (empty($properties) || count($orderBy) > count($properties))
            throw new Exception("Properties array is empty or its count is lesser than orderBy");

        $arrayCollection = $collection->toArray();

        usort($arrayCollection, function($a, $b) use ($properties, $orderBy) {
            foreach ($properties as $index => $property) {
                $aValue = $a->$property;
                $bValue = $b->$property;

                $order = $orderBy[$index] ?? $orderBy[count($orderBy) - 1];

                $comparison = $aValue <=> $bValue;

                if ($order === OrderEnum::DESC)
                    $comparison *= -1;

                if ($comparison !== 0)
                    return $comparison;
            }

            return 0;
        });

        $entityName = $this->GetEntityName('GetById');
        $entityName = strtolower(explode('\\', $entityName)[1]);
        return $this->context->MappingCollection($entityName.'collection', $arrayCollection);
    }
}