<?php

namespace API_DTOEntities_Factory;

use API_DTOEntities_Collection\Parameters;
use API_DTOEntities_Model\Parameter;
use API_DTORepositories\ParameterRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories\ParameterRelationRepository;
use API_RelationRepositories_Collection\ParameterRelations;
use TS_Exception\Classes\DomainException;

class ParameterFactory extends CollectableFactory
{
    private ParameterRelationRepository $parameterRelationRepository;
    private ParameterRelations $parameterRelations;

    public function __construct(ParameterRepository $repository, ParameterRelationRepository $parameterRelationRepository, LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->parameterRelationRepository = $parameterRelationRepository;
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
            $relations = $this->parameterRelationRepository->getBy([['ParamID', 'in', $ids]])->toArray();
        }
        $this->parameterRelations = new ParameterRelations($relations);
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $parameters = [];
        if ($this->collection)
            $parameters = $this->collection->select(fn($n) => new Parameter($n, $this->parameterRelations))->toArray();

        $this->collectable = new Parameters($parameters);
    }
}