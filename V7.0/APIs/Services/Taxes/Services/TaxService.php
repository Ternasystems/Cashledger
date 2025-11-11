<?php

namespace API_Taxes_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\TaxesException;
use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\LanguageRelationRepository;
use API_Taxes_Contract\ITaxService;
use API_TaxesEntities_Collection\Taxes;
use API_TaxesEntities_Model\Tax;
use API_TaxesRepositories\TaxRepository;
use ReflectionException;
use Throwable;
use TS_Exception\Classes\DomainException;

class TaxService implements ITaxService
{
    protected CollectableFactory $factory;
    protected Taxes $taxes;

    /**
     * @throws ReflectionException
     */
    public function __construct(TaxRepository $taxRepository, LanguageRelationRepository $relationRepository)
    {
        $this->factory = new CollectableFactory($taxRepository, $relationRepository);
    }

    /**
     * @throws DomainException
     */
    public function getTaxes(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Tax|Taxes|null
    {
        if (!isset($this->taxes) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->taxes = $this->factory->collectable();
        }

        if (count($this->taxes) === 0)
            return null;

        return $this->taxes->count() > 1 ? $this->taxes : $this->taxes->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function SetTax(array $data): Tax
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main tax DTO
            $tax = new \API_TaxesRepositories_Model\Tax($data['taxData']);
            $this->factory->repository()->add($tax);

            // 2. Get the newly created tax
            $tax = $this->factory->repository()->first([['Label', '=', $data['taxData']['Label']]]);
            if (!$tax)
                throw new TaxesException('tax_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getTaxes([['Id', '=', $tax->Id]], 1, 1, ReloadMode::YES);

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
    public function PutTax(string $id, array $data): ?Tax
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $tax = $this->getTaxes([['Id', '=', $id]])?->first();
            if (!$tax)
                throw new TaxesException('entity_not_found', ["Id" => $id]);

            // 1. Update the main tax record
            foreach ($data as $field => $value)
                $tax->it()->{$field} = $value ?? $tax->it()->{$field};

            $this->factory->repository()->update($tax->it());
            $context->commit();

            return $this->getTaxes([['Id', '=', $id]], 1, 1, ReloadMode::YES);

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
    public function DeleteTax(string $id): bool
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $tax = $this->getTaxes([['Id', '=', $id]])?->first();
            if (!$tax){
                $context->commit();
                return true;
            }

            $this->factory->repository()->deactivate($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}