<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\InventNotes;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\InventNote;
use API_InventoryRepositories\InventNoteRepository;
use API_RelationRepositories\InventRelationRepository;
use Exception;
use ReflectionException;

class InventNoteFactory extends CollectableFactory
{
    protected StockFactory $stockFactory;
    protected InventRelationRepository $inventRelations;

    /**
     * @throws Exception
     */
    public function __construct(InventNoteRepository $repository, StockFactory $_stockFactory, InventRelationRepository $_inventRelations)
    {
        parent::__construct($repository, null);
        $this->stockFactory = $_stockFactory;
        $this->inventRelations = $_inventRelations;
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
            $inventRelations = $this->inventRelations->GetAll()->Where(fn($n) => $n->InventId == $item->Id);
            $stockArray = [];
            foreach ($inventRelations as $inventRelation)
                $stockArray[] = $stocks->FirstOrDefault(fn($n) => $n->It()->Id == $inventRelation->StockId);
            //
            $_stocks = new Stocks($stockArray);
            $colArray[] = new InventNote($item, $_stocks);
        }

        $this->collectable = new InventNotes($colArray);
    }

    public function Collectable(): ?InventNotes
    {
        return $this->collectable ?? null;
    }

    public function Repository(): InventNoteRepository
    {
        return $this->repository;
    }
}