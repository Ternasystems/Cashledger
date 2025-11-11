<?php

namespace API_Billing_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\BillingException;
use API_Assets\Classes\EntityException;
use API_Billing_Contract\IPriceService;
use API_BillingEntities_Collection\Prices;
use API_BillingEntities_Factory\PriceFactory;
use API_BillingEntities_Model\Price;
use API_RelationRepositories\PriceRelationRepository;
use API_RelationRepositories_Model\PriceRelation;
use Throwable;
use TS_Exception\Classes\DomainException;

class PriceService implements IPriceService
{
    protected PriceFactory $priceFactory;
    protected Prices $prices;
    protected PriceRelationRepository $priceRelationRepository;

    public function __construct(PriceFactory $_priceFactory, PriceRelationRepository $_priceRelationRepository)
    {
        $this->priceFactory = $_priceFactory;
        $this->priceRelationRepository = $_priceRelationRepository;
    }

    /**
     * @throws DomainException
     */
    public function getPrices(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Price|Prices|null
    {
        if (!isset($this->prices) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->priceFactory->filter($filter, $pageSize, $offset);
            $this->priceFactory->Create();
            $this->prices = $this->priceFactory->collectable();
        }

        if (count($this->prices) === 0)
            return null;

        return $this->prices->count() > 1 ? $this->prices : $this->prices->first();
    }

    /**
     * @throws DomainException
     * @throws BillingException
     * @throws Throwable
     */
    public function setPrice(array $data): Price
    {
        $context = $this->priceFactory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main price
            $price = new \API_BillingRepositories_Model\Price($data['priceData']);
            $this->priceFactory->repository()->add($price);

            // 2. Get the newly created price
            $price = $this->priceFactory->repository()->first([['Name', '=', $data['priceData']['Name']]]);
            if (!$price)
                throw new BillingException('price_creation_failed');

            if (isset($data['priceRelations'])){
                foreach ($data['priceRelations'] as $priceRelation){
                    $priceRelation['PriceId'] = $price->Id;
                    $relation = new PriceRelation($priceRelation);
                    $this->priceRelationRepository->add($relation);
                }
            }

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getPrices([['Id', '=', $price->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     * @throws EntityException
     * @throws BillingException
     */
    public function putPrice(string $id, array $data): ?Price
    {
        $context = $this->priceFactory->repository()->context;
        $context->beginTransaction();

        try{
            $price = $this->getPrices([['Id', '=', $id]])?->first();
            if (!$price)
                throw new BillingException('entity_not_found', ["Id" => $id]);

            // 1. Update the main price record
            foreach ($data as $field => $value)
                $price->it()->{$field} = $value ?? $price->it()->{$field};

            $this->priceFactory->repository()->update($price->it());

            // Delete the price relations
            if ($price->priceRelations()){
                $priceRelations = $price->priceRelations();
                foreach ($priceRelations as $relation)
                    $this->priceRelationRepository->remove($relation);
            }

            // Update the price relations
            if ($data['priceRelations']){
                foreach ($data['priceRelations'] as $priceRelation) {
                    $priceRelation['PriceId'] = $id;
                    $relation = new PriceRelation($priceRelation);
                    $this->priceRelationRepository->add($relation);
                }
            }

            $context->commit();

            return $this->getPrices([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     * @throws DomainException
     */
    public function deletePrice(string $id): bool
    {
        $context = $this->priceFactory->repository()->context;
        $context->beginTransaction();

        try{
            $price = $this->getPrices([['Id', '=', $id]])?->first();
            if (!$price){
                $context->commit();
                return true;
            }

            // Deactivate the price relations
            if ($price->priceRelations()){
                $priceRelations = $price->priceRelations();
                foreach ($priceRelations as $relation)
                    $this->priceRelationRepository->remove($relation->Id);
            }

            $this->priceFactory->repository()->remove($id);

            $context->commit();
            return true;

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }
}