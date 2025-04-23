<?php

namespace API_Profiling_Service;

use API_Profiling_Contract\ICivilityService;
use API_ProfilingEntities_Collection\Civilities;
use API_ProfilingEntities_Collection\Genders;
use API_ProfilingEntities_Collection\Occupations;
use API_ProfilingEntities_Collection\Statuses;
use API_ProfilingEntities_Collection\Titles;
use API_ProfilingEntities_Factory\CivilityFactory;
use API_ProfilingEntities_Factory\GenderFactory;
use API_ProfilingEntities_Factory\OccupationFactory;
use API_ProfilingEntities_Factory\StatusFactory;
use API_ProfilingEntities_Factory\TitleFactory;
use API_ProfilingEntities_Model\Civility;
use API_ProfilingEntities_Model\Gender;
use API_ProfilingEntities_Model\Occupation;
use API_ProfilingEntities_Model\Status;
use API_ProfilingEntities_Model\Title;
use API_RelationRepositories\CivilityRelationRepository;
use API_RelationRepositories\GenderRelationRepository;
use API_RelationRepositories\OccupationRelationRepository;
use API_RelationRepositories\StatusRelationRepository;
use API_RelationRepositories\TitleRelationRepository;
use Exception;

class CivilityService implements ICivilityService
{
    protected Civilities $civilities;
    protected Genders $genders;
    protected Occupations $occupations;
    protected Statuses $statuses;
    protected Titles $titles;
    protected CivilityRelationRepository $civilityRelationRepository;
    protected GenderRelationRepository $genderRelationRepository;
    protected OccupationRelationRepository $occupationRelationRepository;
    protected StatusRelationRepository $statusRelationRepository;
    protected TitleRelationRepository $titleRelationRepository;

    /**
     * @throws Exception
     */
    public function __construct(CivilityFactory $civilityFactory, GenderFactory $genderFactory, OccupationFactory $occupationFactory, StatusFactory $statusFactory,
                                TitleFactory $titleFactory, CivilityRelationRepository $_civilityRelationRepository, GenderRelationRepository $_genderRelationRepository,
                                OccupationRelationRepository $_occupationRelationRepository, StatusRelationRepository $_statusRelationRepository,
                                TitleRelationRepository $_titleRelationRepository)
    {
        $civilityFactory->Create();
        $this->civilities = $civilityFactory->Collectable();
        $genderFactory->Create();
        $this->genders = $genderFactory->Collectable();
        $occupationFactory->Create();
        $this->occupations = $occupationFactory->Collectable();
        $statusFactory->Create();
        $this->statuses = $statusFactory->Collectable();
        $titleFactory->Create();
        $this->titles = $titleFactory->Collectable();
        $this->civilityRelationRepository = $_civilityRelationRepository;
        $this->genderRelationRepository = $_genderRelationRepository;
        $this->occupationRelationRepository = $_occupationRelationRepository;
        $this->statusRelationRepository = $_statusRelationRepository;
        $this->titleRelationRepository = $_titleRelationRepository;
    }

    public function GetCivilities(callable $predicate = null): Civility|Civilities|null
    {
        if (is_null($predicate))
            return $this->civilities;

        $collection = $this->civilities->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    public function GetGenders(callable $predicate = null): Gender|Genders|null
    {
        if (is_null($predicate))
            return $this->genders;

        $collection = $this->genders->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    public function GetOccupations(callable $predicate = null): Occupation|Occupations|null
    {
        if (is_null($predicate))
            return $this->occupations;

        $collection = $this->occupations->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    public function GetTitles(callable $predicate = null): Title|Titles|null
    {
        if (is_null($predicate))
            return $this->titles;

        $collection = $this->titles->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    public function GetStatuses(callable $predicate = null): Status|Statuses|null
    {
        if (is_null($predicate))
            return $this->statuses;

        $collection = $this->statuses->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    public function GetRelationRepositories(): array
    {
        return array(
            'Civilities' => $this->civilityRelationRepository,
            'Genders' => $this->genderRelationRepository,
            'Occupations' => $this->occupationRelationRepository,
            'Statuses' => $this->statusRelationRepository,
            'Titles' => $this->titleRelationRepository
        );
    }
}