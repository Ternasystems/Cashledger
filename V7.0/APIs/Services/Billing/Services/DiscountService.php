<?php

namespace API_Billing_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\BillingException;
use API_Billing_Contract\IDiscountService;
use API_BillingEntities_Collection\Discounts;
use API_BillingEntities_Model\Discount;
use API_BillingRepositories\DiscountRepository;
use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\LanguageRelationRepository;
use ReflectionException;
use Throwable;
use TS_Exception\Classes\DomainException;

class DiscountService implements IDiscountService
{
    protected CollectableFactory $factory;
    protected Discounts $discounts;

    /**
     * @throws ReflectionException
     */
    public function __construct(DiscountRepository $discountRepository, LanguageRelationRepository $relationRepository)
    {
        $this->factory = new CollectableFactory($discountRepository, $relationRepository);
    }

    /**
     * @throws DomainException
     */
    public function getDiscounts(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Discount|Discounts|null
    {
        if (!isset($this->discounts) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->discounts = $this->factory->collectable();
        }

        if (count($this->discounts) === 0)
            return null;

        return $this->discounts->count() > 1 ? $this->discounts : $this->discounts->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws Throwable
     */
    public function setDiscount(array $data): Discount
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main discount DTO
            $discount = new \API_BillingRepositories_Model\Discount($data['discountData']);
            $this->factory->repository()->add($discount);

            // 2. Get the newly created discount
            $discount = $this->factory->repository()->first([['Label', '=', $data['discountData']['Label']]]);
            if (!$discount)
                throw new BillingException('discount_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getDiscounts([['Id', '=', $discount->Id]], 1, 1, ReloadMode::YES);

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
    public function putDiscount(string $id, array $data): ?Discount
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $discount = $this->getDiscounts([['Id', '=', $id]])?->first();
            if (!$discount)
                throw new BillingException('entity_not_found', ["Id" => $id]);

            // 1. Update the main discount record
            foreach ($data as $field => $value)
                $discount->it()->{$field} = $value ?? $discount->it()->{$field};

            $this->factory->repository()->update($discount->it());
            $context->commit();

            return $this->getDiscounts([['Id', '=', $id]], 1, 1, ReloadMode::YES);

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
    public function deleteDiscount(string $id): bool
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $discount = $this->getDiscounts([['Id', '=', $id]])?->first();
            if (!$discount){
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