<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\Inventories;
use API_InventoryEntities_Model\Inventory;
use API_InventoryRepositories\InventoryRepository;
use API_InventoryRepositories\UnitRepository;
use API_ProfilingEntities_Factory\CredentialFactory;
use API_RelationRepositories\InventoryRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use Exception;

class InventoryFactory extends CollectableFactory
{
    protected StockFactory $stockFactory;
    protected DeliveryNoteFactory $deliveryFactory;
    protected DispatchNoteFactory $dispatchFactory;
    protected ReturnNoteFactory $returnFactory;
    protected WasteNoteFactory $wasteFactory;
    protected TransferNoteFactory $transferFactory;
    protected InventNoteFactory $inventFactory;
    protected SupplierFactory $supplierFactory;
    protected CustomerFactory $customerFactory;
    protected CredentialFactory $credentialFactory;
    protected UnitRepository $unitRepository;
    protected InventoryRelationRepository $inventoryRelationRepository;
    protected LanguageRelationRepository $relations;

    /**
     * @throws Exception
     */
    public function __construct(InventoryRepository $repository, StockFactory $_stockFactory, DeliveryNoteFactory $_deliveryFactory, DispatchNoteFactory $_dispatchFactory,
                                ReturnNoteFactory $_returnFactory, WasteNoteFactory $_wasteFactory, TransferNoteFactory $_transferFactory, InventNoteFactory $_inventFactory,
                                SupplierFactory $_supplierFactory, CustomerFactory $_customerFactory, CredentialFactory $_credentialFactory, UnitRepository $_unitRepository,
                                InventoryRelationRepository $_inventoryRelationRepository, ?LanguageRelationRepository $_relations)
    {
        parent::__construct($repository, null);
        $this->stockFactory = $_stockFactory;
        $this->deliveryFactory = $_deliveryFactory;
        $this->dispatchFactory = $_dispatchFactory;
        $this->returnFactory = $_returnFactory;
        $this->wasteFactory = $_wasteFactory;
        $this->transferFactory = $_transferFactory;
        $this->inventFactory = $_inventFactory;
        $this->supplierFactory = $_supplierFactory;
        $this->customerFactory = $_customerFactory;
        $this->credentialFactory = $_credentialFactory;
        $this->unitRepository = $_unitRepository;
        $this->inventoryRelationRepository = $_inventoryRelationRepository;
        $this->relations = $_relations;
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        if (is_null($collection))
            return;

        $colArray = [];
        $this->stockFactory->Create();
        $stocks = $this->stockFactory->Collectable();
        $this->deliveryFactory->Create();
        $deliveryNotes = $this->deliveryFactory->Collectable();
        $this->dispatchFactory->Create();
        $dispatchNotes = $this->dispatchFactory->Collectable();
        $this->returnFactory->Create();
        $returnNotes = $this->returnFactory->Collectable();
        $this->wasteFactory->Create();
        $wasteNotes = $this->wasteFactory->Collectable();
        $this->transferFactory->Create();
        $transferNotes = $this->transferFactory->Collectable();
        $this->inventFactory->Create();
        $inventNotes = $this->inventFactory->Collectable();
        $this->supplierFactory->Create();
        $suppliers = $this->supplierFactory->Collectable();
        $this->customerFactory->Create();
        $customers = $this->customerFactory->Collectable();
        $this->credentialFactory->Create();
        $credentials = $this->credentialFactory->Collectable();
        $factory = new CollectableFactory($this->unitRepository, $this->relations);
        $factory->Create();
        $units = $factory->Collectable();
        //
        foreach ($collection as $item) {
            $unit = $units->FirstOrDefault(fn($n) => $n->It()->Id == $item->UnitId);
            $stock = $stocks->FirstOrDefault(fn($n) => $n->It()->Id == $item->StockId);
            $deliveryNote = $deliveryNotes?->FirstOrDefault(fn($n) => $n->It()->Id == $item->NoteId);
            $dispatchNote = $dispatchNotes?->FirstOrDefault(fn($n) => $n->It()->Id == $item->NoteId);
            $returnNote = $returnNotes?->FirstOrDefault(fn($n) => $n->It()->Id == $item->NoteId);
            $wasteNote = $wasteNotes?->FirstOrDefault(fn($n) => $n->It()->Id == $item->NoteId);
            $transferNote = $transferNotes?->FirstOrDefault(fn($n) => $n->It()->Id == $item->NoteId);
            $inventNote = $inventNotes?->FirstOrDefault(fn($n) => $n->It()->Id == $item->NoteId);
            $supplier = $suppliers->FirstOrDefault(fn($n) => $n->It()->Id == $item->PartnerId);
            $customer = $customers->FirstOrDefault(fn($n) => $n->It()->Id == $item->PartnerId);
            $credential = $credentials->FirstOrDefault(fn($n) => $n->It()->Id == $item->PartnerId);
            $relations = $this->inventoryRelationRepository->GetBy(fn($n) => $n->InventId == $item->Id);
            $colArray[] = new Inventory($item, $unit, $stock, $deliveryNote, $dispatchNote, $returnNote, $wasteNote, $transferNote, $inventNote, $supplier, $customer, $credential,
                $relations);
        }

        $this->collectable = new Inventories($colArray);
    }

    public function Collectable(): ?Inventories
    {
        return $this->collectable ?? null;
    }

    public function Repository(): InventoryRepository
    {
        return $this->repository;
    }
}