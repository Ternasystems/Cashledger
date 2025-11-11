<?php

namespace API_Administration_Service;

use API_Administration_Contract\ICityService;
use API_Assets\Classes\AdministrationException;
use API_Assets\Classes\EntityException;
use API_DTOEntities_Collection\Cities;
use API_DTOEntities_Factory\CityFactory;
use API_DTOEntities_Model\City;
use Throwable;
use TS_Exception\Classes\DomainException;

class CityService implements ICityService
{
    protected CityFactory $cityFactory;
    protected Cities $cities;

    public function __construct(CityFactory $_cityFactory)
    {
        $this->cityFactory = $_cityFactory;
    }

    /**
     * @throws DomainException
     */
    public function getCities(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): City|Cities|null
    {
        if (!isset($this->cities) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->cityFactory->filter($filter, $pageSize, $offset);
            $this->cityFactory->Create();
            $this->cities = $this->cityFactory->collectable();
        }

        if (count($this->cities) === 0)
            return null;

        return $this->cities->count() > 1 ? $this->cities : $this->cities->first();
    }

    /**
     * @throws DomainException
     * @throws AdministrationException
     * @throws Throwable
     */
    public function setCity(array $data): City
    {
        $context = $this->cityFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main city DTO
            $city = new \API_DTORepositories_Model\City($data['cityData']);
            $this->cityFactory->repository()->add($city);

            // 2. Get the newly created city
            $city = $this->cityFactory->repository()->first([['Name', '=', $data['cityData']['Name']], ['CountryId', '=', $data['cityData']['CountryId']]]);
            if (!$city)
                throw new AdministrationException('city_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getCities([['Id', '=', $city->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     * @throws EntityException
     * @throws AdministrationException
     */
    public function putCity(string $id, array $data): ?City
    {
        $context = $this->cityFactory->repository()->context;
        $context->beginTransaction();

        try{
            $city = $this->getCities([['Id', '=', $id]])?->first();
            if (!$city)
                throw new AdministrationException('entity_not_found', ["Id" => $id]);

            // 1. Update the main city record
            foreach ($data as $field => $value)
                $city->it()->{$field} = $value ?? $city->it()->{$field};

            $this->cityFactory->repository()->update($city->it());
            $context->commit();

            return $this->getCities([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function deleteCity(string $id): bool
    {
        $context = $this->cityFactory->repository()->context;
        $context->beginTransaction();

        try{
            $city = $this->getCities([['Id', '=', $id]])?->first();
            if (!$city){
                $context->commit();
                return true;
            }

            $this->cityFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}