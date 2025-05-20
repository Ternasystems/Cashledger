<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\TransferNotes;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\TransferNote;
use API_InventoryRepositories\TransferNoteRepository;
use API_RelationRepositories\TransferRelationRepository;
use Exception;
use ReflectionException;

class TransferNoteFactory extends CollectableFactory
{
    protected StockFactory $stockFactory;
    protected TransferRelationRepository $transferRelations;

    /**
     * @throws Exception
     */
    public function __construct(TransferNoteRepository $repository, StockFactory $_stockFactory, TransferRelationRepository $_transferRelations)
    {
        parent::__construct($repository, null);
        $this->stockFactory = $_stockFactory;
        $this->transferRelations = $_transferRelations;
    }

    /**
     * @throws ReflectionException
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
        //
        foreach ($collection as $item) {
            $transferRelations = $this->transferRelations->GetAll()->Where(fn($n) => $n->InventId == $item->Id);
            $stockArray = [];
            foreach ($transferRelations as $transferRelation)
                $stockArray[] = $stocks->FirstOrDefault(fn($n) => $n->It()->Id == $transferRelation->StockId);
            //
            $_stocks = new Stocks($stockArray);
            $colArray[] = new TransferNote($item, $_stocks);
        }

        $this->collectable = new TransferNotes($colArray);
    }

    public function Collectable(): ?TransferNotes
    {
        return $this->collectable ?? null;
    }

    public function Repository(): TransferNoteRepository
    {
        return $this->repository;
    }
}