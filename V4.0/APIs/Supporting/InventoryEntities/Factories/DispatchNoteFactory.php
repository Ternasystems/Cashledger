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
    protected Stocks $stocks;
    protected DispatchRelationRepository $dispatchRelations;

    /**
     * @throws Exception
     */
    public function __construct(DispatchNoteRepository $repository, StockFactory $_stockFactory, DispatchRelationRepository $_dispatchRelations)
    {
        parent::__construct($repository, null);
        $_stockFactory->Create();
        $this->stocks = $_stockFactory->Collectable();
        $this->dispatchRelations = $_dispatchRelations;
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item) {
            $dispatchRelations = $this->dispatchRelations->GetAll()->Where(fn($n) => $n->DispatchId == $item->Id);
            $stockArray = [];
            foreach ($dispatchRelations as $dispatchRelation)
                $stockArray[] = $this->stocks->FirstOrDefault(fn($n) => $n->Id == $dispatchRelation->StockId);
            //
            $_stocks = new Stocks($stockArray);
            $colArray[] = new DispatchNote($item, $_stocks);
        }

        $this->collectable = new DispatchNotes($colArray);
    }

    public function Collectable(): ?DispatchNotes
    {
        return $this->collectable;
    }

    public function Repository(): DispatchNoteRepository
    {
        return $this->repository;
    }
}