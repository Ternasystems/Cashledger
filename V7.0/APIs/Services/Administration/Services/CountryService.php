<?php

namespace API_Administration_Service;

use API_Administration_Contract\ICountryService;
use API_Assets\Classes\AdministrationException;
use API_Assets\Classes\EntityException;
use API_DTOEntities_Collection\Countries;
use API_DTOEntities_Factory\CountryFactory;
use API_DTOEntities_Model\Country;
use Throwable;
use TS_Exception\Classes\DomainException;

class CountryService implements ICountryService
{
    protected CountryFactory $countryFactory;
    protected Countries $countries;

    public function __construct(CountryFactory $_countryFactory)
    {
        $this->countryFactory = $_countryFactory;
    }

    /**
     * @throws DomainException
     */
    public function getCountries(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Country|Countries|null
    {
        if (!isset($this->countries) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->countryFactory->filter($filter, $pageSize, $offset);
            $this->countryFactory->Create();
            $this->countries = $this->countryFactory->collectable();
        }

        if (count($this->countries) === 0)
            return null;

        return $this->countries->count() > 1 ? $this->countries : $this->countries->first();
    }

    /**
     * @throws DomainException
     * @throws AdministrationException
     * @throws Throwable
     */
    public function setCountry(array $data): Country
    {
        $context = $this->countryFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main country DTO
            $country = new \API_DTORepositories_Model\Country($data['countryData']);
            $this->countryFactory->repository()->add($country);

            // 2. Get the newly created country
            $country = $this->countryFactory->repository()->first([['ISO3', '=', $data['countryData']['ISO3']]]);
            if (!$country)
                throw new AdministrationException('country_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getCountries([['Id', '=', $country->Id]], 1, 1, ReloadMode::YES);

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
    public function putCountry(string $id, array $data): ?Country
    {
        $context = $this->countryFactory->repository()->context;
        $context->beginTransaction();

        try{
            $country = $this->getCountries([['Id', '=', $id]])?->first();
            if (!$country)
                throw new AdministrationException('entity_not_found', ["Id" => $id]);

            // 1. Update the main country record
            foreach ($data as $field => $value)
                $country->it()->{$field} = $value ?? $country->it()->{$field};

            $this->countryFactory->repository()->update($country->it());
            $context->commit();

            return $this->getCountries([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function deleteCountry(string $id): bool
    {
        $context = $this->countryFactory->repository()->context;
        $context->beginTransaction();

        try{
            $country = $this->getCountries([['Id', '=', $id]])?->first();
            if (!$country){
                $context->commit();
                return true;
            }

            $this->countryFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}