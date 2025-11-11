<?php

namespace API_Administration_Service;

use API_Administration_Contract\IContinentService;
use API_Assets\Classes\AdministrationException;
use API_DTOEntities_Collection\Continents;
use API_DTOEntities_Factory\CollectableFactory;
use API_DTOEntities_Model\Continent;
use API_DTORepositories\ContinentRepository;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use Throwable;
use TS_Exception\Classes\DomainException;

class ContinentService implements IContinentService
{
    protected CollectableFactory $factory;
    protected Continents $continents;

    /**
     * @throws ReflectionException
     */
    public function __construct(ContinentRepository $continentRepository, LanguageRelationRepository $relationRepository)
    {
        $this->factory = new CollectableFactory($continentRepository, $relationRepository);
    }
    
    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getContinents(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Continent|Continents|null
    {
        if (!isset($this->continents) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->continents = $this->factory->collectable();
        }

        if (count($this->continents) === 0)
            return null;

        return $this->continents->count() > 1 ? $this->continents : $this->continents->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function setContinent(array $data): Continent
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main continent DTO
            $continent = new \API_DTORepositories_Model\Continent($data['continentData']);
            $this->factory->repository()->add($continent);

            // 2. Get the newly created continent
            $continent = $this->factory->repository()->first([['Name', '=', $data['continentData']['Name']]]);
            if (!$continent)
                throw new AdministrationException('continent_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getContinents([['Id', '=', $continent->Id]], 1, 1, ReloadMode::YES);

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
    public function putContinent(string $id, array $data): ?Continent
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $continent = $this->getContinents([['Id', '=', $id]])?->first();
            if (!$continent)
                throw new AdministrationException('entity_not_found', ["Id" => $id]);

            // 1. Update the main continent record
            foreach ($data as $field => $value)
                $continent->it()->{$field} = $value ?? $continent->it()->{$field};

            $this->factory->repository()->update($continent->it());
            $context->commit();

            return $this->getContinents([['Id', '=', $id]], 1, 1, ReloadMode::YES);

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
    public function deleteContinent(string $id): bool
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $appCategory = $this->getContinents([['Id', '=', $id]])?->first();
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