<?php

namespace API_Inventory_Service;

use API_Inventory_Contract\IDispatchService;
use API_Inventory_Contract\IInventoryService;
use API_Inventory_Contract\IStockService;
use API_InventoryEntities_Collection\DispatchNotes;
use API_InventoryEntities_Factory\DispatchNoteFactory;
use API_InventoryEntities_Model\DispatchNote;
use API_RelationRepositories\DispatchRelationRepository;
use API_RelationRepositories_Model\DispatchRelation;
use Exception;
use ReflectionException;

class DispatchService implements IDispatchService
{
    protected DispatchNoteFactory $dispatchFactory;
    protected DispatchRelationRepository $dispatchRelationRepository;
    protected IStockService $stockService;
    protected IInventoryService $inventoryService;

    public function __construct(DispatchNoteFactory $_dispatchFactory, DispatchRelationRepository $_dispatchRelationRepository, IStockService $_stockService,
                                IInventoryService $_inventoryService)
    {
        $this->dispatchFactory = $_dispatchFactory;
        $this->dispatchRelationRepository = $_dispatchRelationRepository;
        $this->stockService = $_stockService;
        $this->inventoryService = $_inventoryService;
    }

    /**
     * @throws ReflectionException
     */
    public function GetDispatches(callable $predicate = null): DispatchNote|DispatchNotes|null
    {
        $this->dispatchFactory->Create();

        if (is_null($predicate))
            return $this->dispatchFactory->Collectable();

        $collection = $this->dispatchFactory->Collectable()->Where($predicate);

        return $collection->count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function SetDispatch(object $model): void
    {
        // Set dispatch note
        $repository = $this->dispatchFactory->Repository();
        $repository->Add(\API_InventoryRepositories_Model\DispatchNote::class, array($model->dispatchnumber, $model->dispatchreference,
            $model->dispatchdate->format('Y-m-d H:i:s'), $model->dispatchdesc));
        $dispatchId = $repository->GetBy(fn($n) => $n->DispatchNumber == $model->dispatchnumber)->FirstOrDefault()->Id;

        // Set stocks
        foreach ($model->stocks as $stock){
            foreach ($stock as $stockModel){
                $this->stockService->DispatchStock($stockModel);
                $stockId = $stockModel->stockid;

                // Set delivery relation
                $this->dispatchRelationRepository->Add(DispatchRelation::class, array($stockId, $dispatchId));

                // Set inventory
                $stockInvent = $stockModel->stockinvent;
                $stockInvent->noteid = $dispatchId;
                $stockInvent->stockid = $stockId;
                $this->inventoryService->SetInventory($stockInvent);
            }
        }
        $this->dispatchFactory->Create();
    }

    public function PutDispatch(object $model): void
    {
        // TODO: Implement PutDispatch() method.
    }

    public function DeleteDispatch(string $id): void
    {
        // TODO: Implement DeleteDispatch() method.
    }
}