<?php

namespace API_Profiling_Controller;

use API_DTOEntities_Collection\EntityCollectable;
use API_Profiling_Contract\ICivilityService;
use API_ProfilingEntities_Model\Civility;
use API_ProfilingEntities_Model\Gender;
use API_ProfilingEntities_Model\Occupation;
use API_ProfilingEntities_Model\Status;
use API_ProfilingEntities_Model\Title;
use Exception;
use TS_Controller\Classes\BaseController;

class CivilityController extends BaseController
{
    private ICivilityService $service;

    public function __construct(ICivilityService $_service)
    {
        $this->service = $_service;
    }

    /**
     * @throws Exception
     */
    public function Get(): ?array
    {
        return [
            'Civilities' => $this->service->GetCivilities(),
            'Genders' => $this->service->GetGenders(),
            'Occupations' => $this->service->GetOccupations(),
            'Titles' => $this->service->GetTitles(),
            'Statuses' => $this->service->GetStatuses()
        ];
    }

    public function GetAll(string $type): ?EntityCollectable
    {
        return match ($type){
            'Civilities' => $this->service->GetCivilities(),
            'Genders' => $this->service->GetGenders(),
            'Occupations' => $this->service->GetOccupations(),
            'Titles' => $this->service->GetTitles(),
            'Statuses' => $this->service->GetStatuses()
        };
    }
}