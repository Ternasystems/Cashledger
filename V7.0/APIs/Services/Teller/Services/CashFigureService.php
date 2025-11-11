<?php

namespace API_Teller_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\TellerException;
use API_DTOEntities_Factory\CollectableFactory;
use API_RelationRepositories\LanguageRelationRepository;
use API_Teller_Contract\ICashFigureService;
use API_TellerEntities_Collection\CashFigures;
use API_TellerEntities_Model\CashFigure;
use API_TellerRepositories\CashFigureRepository;
use ReflectionException;
use Throwable;
use TS_Exception\Classes\DomainException;

class CashFigureService implements ICashFigureService
{
    protected CashFigureRepository $cashFigureRepository;
    protected CollectableFactory $factory;
    protected CashFigures $cashFigures;
    protected LanguageRelationRepository $relationRepository;

    /**
     * @throws ReflectionException
     */
    function __construct(CashFigureRepository $_cashFigureRepository, LanguageRelationRepository $_relationRepository)
    {
        $this->cashFigureRepository = $_cashFigureRepository;
        $this->relationRepository = $_relationRepository;

        $this->factory = new CollectableFactory($this->cashFigureRepository, $this->relationRepository);
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getCashFigures(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): CashFigure|CashFigures|null
    {
        if (!isset($this->cashFigures) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->factory->filter($filter, $pageSize, $offset);
            $this->factory->Create();
            $this->cashFigures = $this->factory->collectable();
        }

        if (count($this->cashFigures) === 0)
            return null;

        return $this->cashFigures->count() > 1 ? $this->cashFigures : $this->cashFigures->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws TellerException
     * @throws Throwable
     */
    public function SetCashFigure(array $data): CashFigure
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            // 1. Create and save the main cashFigure DTO
            $cashFigure = new \API_TellerRepositories_Model\CashFigure($data['cashFigureData']);
            $this->factory->repository()->add($cashFigure);

            // 2. Get the newly created cashFigure
            $cashFigure = $this->factory->repository()->first([['Name', '=', $data['cashFigureData']['Name']]]);
            if (!$cashFigure)
                throw new TellerException('cashFigure_creation_failed');

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getCashFigures([['Id', '=', $cashFigure->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws TellerException
     * @throws Throwable
     */
    public function PutCashFigure(string $id, array $data): ?CashFigure
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $cashFigure = $this->getCashFigures([['Id', '=', $id]])?->first();
            if (!$cashFigure)
                throw new TellerException('cashFigure_not_found', ["Id" => $id]);

            // 1. Update the main cashFigure record
            foreach ($data as $field => $value)
                $cashFigure->it()->{$field} = $value ?? $cashFigure->it()->{$field};

            $this->factory->repository()->update($cashFigure->it());
            $context->commit();

            return $this->getCashFigures([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e){
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws DomainException
     * @throws TellerException
     * @throws Throwable
     */
    public function DeleteCashFigure(string $id): bool
    {
        $this->factory->Create();
        $context = $this->factory->repository()->context;
        $context->beginTransaction();

        try{
            $cashFigure = $this->getCashFigures([['Id', '=', $id]])?->first();
            if (!$cashFigure){
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