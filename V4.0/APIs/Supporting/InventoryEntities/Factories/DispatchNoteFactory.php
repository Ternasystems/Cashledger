<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\DispatchNotes;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\DispatchNote;
use API_InventoryRepositories\DispatchNoteRepository;
use API_RelationRepositories\DispatchRelationRepository;
use Exception;
use ReflectionException;

class DispatchNoteFactory extends CollectableFactory
{
    protected StockFactory $stockFactory;
    protected DispatchRelationRepository $dispatchRelations;

    /**
     * @throws Exception
     */
    public function __construct(DispatchNoteRepository $repository, StockFactory $_stockFactory, DispatchRelationRepository $_dispatchRelations)
    {
        parent::__construct($repository, null);
        $this->stockFactory = $_stockFactory;
        $this->dispatchRelations = $_dispatchRelations;
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
            $dispatchRelations = $this->dispatchRelations->GetAll()->Where(fn($n) => $n->DispatchId == $item->Id);
            $stockArray = [];
            foreach ($dispatchRelations as $dispatchRelation)
                $stockArray[] = $stocks->FirstOrDefault(fn($n) => $n->It()->Id == $dispatchRelation->StockId);
            //
            $_stocks = new Stocks($stockArray);
            $colArray[] = new DispatchNote($item, $_stocks);
        }

        $this->collectable = new DispatchNotes($colArray);
    }

    public function Collectable(): ?DispatchNotes
    {
        return $this->collectable ?? null;
    }

    public function Repository(): DispatchNoteRepository
    {
        return $this->repository;
    }
}