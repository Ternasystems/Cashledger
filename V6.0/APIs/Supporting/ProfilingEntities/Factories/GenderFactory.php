<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Genders;
use API_ProfilingEntities_Model\Gender;
use API_ProfilingRepositories\GenderRepository;
use API_RelationRepositories\GenderRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Collection\GenderRelations;
use TS_Exception\Classes\DomainException;

class GenderFactory extends CollectableFactory
{
    private GenderRelationRepository $genderRelationRepository;
    private GenderRelations $genderRelations;

    public function __construct(GenderRepository $repository, GenderRelationRepository $genderRelationRepository, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->genderRelationRepository = $genderRelationRepository;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        $relations = [];
        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->Id)->toArray();
            $relations = $this->genderRelationRepository->getBy([['GenderID', 'in', $ids]]);
        }
        $this->genderRelations = new GenderRelations($relations);
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $genders = [];
        if ($this->collection)
            $genders = $this->collection->select(fn($n) => new Gender($n, $this->genderRelations))->toArray();

        $this->collectable = new Genders($genders);
    }
}