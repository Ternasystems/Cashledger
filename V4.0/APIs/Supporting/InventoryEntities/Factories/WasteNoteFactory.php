<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\WasteNotes;
use API_InventoryEntities_Collection\Stocks;
use API_InventoryEntities_Model\WasteNote;
use API_InventoryRepositories\WasteNoteRepository;
use API_RelationRepositories\WasteRelationRepository;
use Exception;
use ReflectionException;

class WasteNoteFactory extends CollectableFactory
{
    protected StockFactory $stockFactory;
    protected WasteRelationRepository $wasteRelations;

    /**
     * @throws Exception
     */
    public function __construct(WasteNoteRepository $repository, StockFactory $_stockFactory, WasteRelationRepository $_wasteRelations)
    {
        parent::__construct($repository, null);
        $this->stockFactory = $_stockFactory;
        $this->wasteRelations = $_wasteRelations;
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
            $wasteRelations = $this->wasteRelations->GetAll()->Where(fn($n) => $n->WasteId == $item->Id);
            $stockArray = [];
            foreach ($wasteRelations as $wasteRelation)
                $stockArray[] = $stocks->FirstOrDefault(fn($n) => $n->It()->Id == $wasteRelation->StockId);
            //
            $_stocks = new Stocks($stockArray);
            $colArray[] = new WasteNote($item, $_stocks);
        }

        $this->collectable = new WasteNotes($colArray);
    }

    public function Collectable(): ?WasteNotes
    {
        return $this->collectable ?? null;
    }

    public function Repository(): WasteNoteRepository
    {
        return $this->repository;
    }
}