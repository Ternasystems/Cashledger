<?php

namespace API_Inventory_Service;

use API_Inventory_Contract\IDeliveryService;
use API_Inventory_Contract\IInventoryService;
use API_Inventory_Contract\IStockService;
use API_InventoryEntities_Collection\DeliveryNotes;
use API_InventoryEntities_Factory\DeliveryNoteFactory;
use API_InventoryEntities_Model\DeliveryNote;
use API_RelationRepositories\DeliveryRelationRepository;
use API_RelationRepositories_Model\DeliveryRelation;
use ReflectionException;

class DeliveryService implements IDeliveryService
{
    protected DeliveryNoteFactory $deliveryFactory;
    protected DeliveryRelationRepository $deliveryRelationRepository;
    protected IStockService $stockService;
    protected IInventoryService $inventoryService;

    public function __construct(DeliveryNoteFactory $_deliveryFactory, DeliveryRelationRepository $_deliveryRelationRepository, IStockService $_stockService,
                                IInventoryService $_inventoryService)
    {
        $this->deliveryFactory = $_deliveryFactory;
        $this->deliveryRelationRepository = $_deliveryRelationRepository;
        $this->stockService = $_stockService;
        $this->inventoryService = $_inventoryService;
    }

    /**
     * @throws ReflectionException
     */
    public function GetDeliveries(callable $predicate = null): DeliveryNote|DeliveryNotes|null
    {
        $this->deliveryFactory->Create();

        if (is_null($predicate))
            return $this->deliveryFactory->Collectable();

        $collection = $this->deliveryFactory->Collectable()->Where($predicate);

        return $collection->count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     */
    public function SetDelivery(object $model): void
    {
        // Set delivery note
        $repository = $this->deliveryFactory->Repository();
        $repository->Add(\API_InventoryRepositories_Model\DeliveryNote::class, array($model->deliverynumber, $model->deliveryreference,
            $model->deliverydate->format('Y-m-d H:i:s'), $model->deliverydesc));
        $deliveryId = $repository->GetBy(fn($n) => $n->DeliveryNumber == $model->deliverynumber)->FirstOrDefault()->Id;

        // Set stocks
        foreach ($model->warehouses as $warehouse) {
            foreach ($warehouse as $stockModel) {
                $this->stockService->SetStock($stockModel);
                $stockId = $this->stockService->GetStocks(fn($n) => $n->It()->ProductId == $stockModel->productid && $n->It()->WarehouseId == $stockModel->warehouseid &&
                    $n->It()->PackagingId == $stockModel->packagingid && $n->It()->BatchNumber == $stockModel->batchnumber)->It()->Id;

                // Set delivery relation
                $this->deliveryRelationRepository->Add(DeliveryRelation::class, array($stockId, $deliveryId));

                // Set inventory
                $stockInvent = $stockModel->stockinvent;
                $stockInvent->noteid = $deliveryId;
                $stockInvent->stockid = $stockId;
                $this->inventoryService->SetInventory($stockInvent);
            }
        }
        //$this->deliveryFactory->Create();
    }

    public function PutDelivery(object $model): void
    {
        // TODO: Implement PutDelivery() method.
    }

    public function DeleteDelivery(string $id): void
    {
        // TODO: Implement DeleteDelivery() method.
    }
}