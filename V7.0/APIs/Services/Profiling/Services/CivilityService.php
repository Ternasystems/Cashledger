<?php

namespace API_Profiling_Service;

use API_Administration_Service\ReloadMode;
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
use TS_Exception\Classes\DomainException;

class CivilityService implements ICivilityService
{
    protected CivilityFactory $civilityFactory;
    protected Civilities $civilities;
    protected GenderFactory $genderFactory;
    protected Genders $genders;
    protected OccupationFactory $occupationFactory;
    protected Occupations $occupations;
    protected StatusFactory $statusFactory;
    protected Statuses $statuses;
    protected TitleFactory $titleFactory;
    protected Titles $titles;

    public function __construct(CivilityFactory $civilityFactory, GenderFactory $genderFactory, OccupationFactory $occupationFactory, StatusFactory $statusFactory,
                                TitleFactory $titleFactory)
    {
        $this->civilityFactory = $civilityFactory;
        $this->genderFactory = $genderFactory;
        $this->occupationFactory = $occupationFactory;
        $this->statusFactory = $statusFactory;
        $this->titleFactory = $titleFactory;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function GetCivilities(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Civility|Civilities|null
    {
        if (!isset($this->civilities) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->civilityFactory->filter($filter, $pageSize, $offset);
            $this->civilityFactory->Create();
            $this->civilities = $this->civilityFactory->collectable();
        }

        if (count($this->civilities) === 0)
            return null;

        return $this->civilities->count() > 1 ? $this->civilities : $this->civilities->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getGenders(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Gender|Genders|null
    {
        if (!isset($this->genders) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->genderFactory->filter($filter, $pageSize, $offset);
            $this->genderFactory->Create();
            $this->genders = $this->genderFactory->collectable();
        }

        if (count($this->genders) === 0)
            return null;

        return $this->genders->count() > 1 ? $this->genders : $this->genders->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getOccupations(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Occupation|Occupations|null
    {
        if (!isset($this->occupations) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->occupationFactory->filter($filter, $pageSize, $offset);
            $this->occupationFactory->Create();
            $this->occupations = $this->occupationFactory->collectable();
        }

        if (count($this->occupations) === 0)
            return null;

        return $this->occupations->count() > 1 ? $this->occupations : $this->occupations->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getTitles(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Title|Titles|null
    {
        if (!isset($this->titles) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->titleFactory->filter($filter, $pageSize, $offset);
            $this->titleFactory->Create();
            $this->titles = $this->titleFactory->collectable();
        }

        if (count($this->titles) === 0)
            return null;

        return $this->titles->count() > 1 ? $this->titles : $this->titles->first();
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getStatuses(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Status|Statuses|null
    {
        if (!isset($this->statuses) || $reloadMode == ReloadMode::YES){
            // Calculate the offset for the database query.
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            // Apply the filter and pagination parameters to the factory.
            $this->statusFactory->filter($filter, $pageSize, $offset);
            $this->statusFactory->Create();
            $this->statuses = $this->statusFactory->collectable();
        }

        if (count($this->statuses) === 0)
            return null;

        return $this->statuses->count() > 1 ? $this->statuses : $this->statuses->first();
    }
}