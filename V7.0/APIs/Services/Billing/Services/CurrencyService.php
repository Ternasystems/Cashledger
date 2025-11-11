<?php

namespace API_Billing_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\BillingException;
use API_Billing_Contract\ICurrencyService;
use API_BillingEntities_Collection\Currencies;
use API_BillingEntities_Model\Currency;
use API_BillingRepositories\CurrencyRepository;
use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use Throwable;
use TS_Exception\Classes\DomainException;

class CurrencyService implements ICurrencyService
{
    protected CollectableFactory $factory;
    protected Currencies $currencies;

    /**
     * @throws ReflectionException
     */
    public function __construct(CurrencyRepository $currencyRepository, LanguageRelationRepository $relationRepository)
    {
        $this->factory = new CollectableFactory($currencyRepository, $relationRepository);
    }

    /**
     * @throws DomainException
     */
    public function getCurrencies(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Currency|Currencies|null
    {
        if (!isset($this->currencies) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->currencies = $this->factory->collectable();
        }

        if (count($this->currencies) === 0)
            return null;

        return $this->currencies->count() > 1 ? $this->currencies : $this->currencies->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function setCurrency(array $data): Currency
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main currency DTO
            $currency = new \API_BillingRepositories_Model\Currency($data['currencyData']);
            $this->factory->repository()->add($currency);

            // 2. Get the newly created currency
            $currency = $this->factory->repository()->first([['Label', '=', $data['currencyData']['Label']]]);
            if (!$currency)
                throw new BillingException('currency_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getCurrencies([['Id', '=', $currency->Id]], 1, 1, ReloadMode::YES);

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
    public function putCurrency(string $id, array $data): ?Currency
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $currency = $this->getCurrencies([['Id', '=', $id]])?->first();
            if (!$currency)
                throw new BillingException('entity_not_found', ["Id" => $id]);

            // 1. Update the main currency record
            foreach ($data as $field => $value)
                $currency->it()->{$field} = $value ?? $currency->it()->{$field};

            $this->factory->repository()->update($currency->it());
            $context->commit();

            return $this->getCurrencies([['Id', '=', $id]], 1, 1, ReloadMode::YES);

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
    public function deleteCurrency(string $id): bool
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $currency = $this->getCurrencies([['Id', '=', $id]])?->first();
            if (!$currency){
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