<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Titles;
use API_ProfilingEntities_Model\Title;
use API_ProfilingRepositories\TitleRepository;
use API_RelationRepositories\TitleRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories_Collection\TitleRelations;
use TS_Exception\Classes\DomainException;

class TitleFactory extends CollectableFactory
{
    private TitleRelationRepository $titleRelationRepository;
    private TitleRelations $titleRelations;

    public function __construct(TitleRepository $repository, TitleRelationRepository $titleRelationRepository, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->titleRelationRepository = $titleRelationRepository;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository->getBy($this->whereClause, $this->limit, $this->offset);

        $relations = [];
        if ($this->collection){
            $ids = $this->collection->select(fn($n) => $n->id)->toArray();
            $relations = $this->titleRelationRepository->getBy([['TitleID', 'in', $ids]]);
        }
        $this->titleRelations = new TitleRelations($relations);
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $titles = [];
        if ($this->collection)
            $titles = $this->collection->select(fn($n) => new Title($n, $this->titleRelations))->toArray();

        $this->collectable = new Titles($titles);
    }
}