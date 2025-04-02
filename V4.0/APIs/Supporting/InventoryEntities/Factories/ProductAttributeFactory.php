<?php

namespace API_InventoryEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_InventoryEntities_Collection\ProductAttributes;
use API_InventoryEntities_Model\ProductAttribute;
use API_InventoryRepositories\ProductAttributeRepository;
use API_RelationRepositories\AttributeRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use Exception;

class ProductAttributeFactory extends CollectableFactory
{
    protected AttributeRelationRepository $relations;
    public function __construct(ProductAttributeRepository $repository, AttributeRelationRepository $_relations, ?LanguageRelationRepository $_relationRepository)
    {
        parent::__construct($repository, $_relationRepository);
        $this->relations = $_relations;
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item)
            $colArray[] = new ProductAttribute($item, $this->relations->GetAll(), $this->relationRepository->GetAll());

        $this->collectable = new ProductAttributes($colArray);
    }

    public function Collectable(): ?ProductAttributes
    {
        return $this->collectable;
    }

    public function Repository(): ProductAttributeRepository
    {
        return $this->repository;
    }
}