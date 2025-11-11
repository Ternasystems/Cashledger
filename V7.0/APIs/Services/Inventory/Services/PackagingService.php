<?php

namespace API_Inventory_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\InventoryException;
use API_DTOEntities_Factory\CollectableFactory;
use API_Inventory_Contract\IPackagingService;
use API_InventoryEntities_Collection\Packagings;
use API_InventoryEntities_Model\Packaging;
use API_InventoryRepositories\PackagingRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use Throwable;
use TS_Exception\Classes\DomainException;

class PackagingService implements IPackagingService
{
    protected PackagingRepository $packagingRepository;
    protected CollectableFactory $factory;
    protected Packagings $packagings;
    protected LanguageRelationRepository $relationRepository;

    /**
     * @throws ReflectionException
     */
    function __construct(PackagingRepository $_packagingRepository, LanguageRelationRepository $_relationRepository)
    {
        $this->packagingRepository = $_packagingRepository;
        $this->relationRepository = $_relationRepository;

        $this->factory = new CollectableFactory($this->packagingRepository, $this->relationRepository);
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getPackagings(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Packaging|Packagings|null
    {
        if (!isset($this->packagings) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->packagings = $this->factory->collectable();
        }

        if (count($this->packagings) === 0)
            return null;

        return $this->packagings->count() > 1 ? $this->packagings : $this->packagings->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws InventoryException
     * @throws Throwable
     */
    public function SetPackaging(array $data): Packaging
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main packaging DTO
            $packaging = new \API_InventoryRepositories_Model\Packaging($data['packagingData']);
            $this->factory->repository()->add($packaging);

            // 2. Get the newly created packaging
            $packaging = $this->factory->repository()->first([['Name', '=', $data['packagingData']['Name']]]);
            if (!$packaging)
                throw new InventoryException('packaging_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getPackagings([['Id', '=', $packaging->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws InventoryException
     * @throws Throwable
    */
    public function PutPackaging(string $id, array $data): ?Packaging
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $packaging = $this->getPackagings([['Id', '=', $id]])?->first();
            if (!$packaging)
                throw new InventoryException('packaging_not_found', ["Id" => $id]);

            // 1. Update the main packaging record
            foreach ($data as $field => $value)
                $packaging->it()->{$field} = $value ?? $packaging->it()->{$field};

            $this->factory->repository()->update($packaging->it());
            $context->commit();

            return $this->getPackagings([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws InventoryException
     * @throws Throwable
     */
    public function DeletePackaging(string $id): bool
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $packaging = $this->getPackagings([['Id', '=', $id]])?->first();
            if (!$packaging){
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