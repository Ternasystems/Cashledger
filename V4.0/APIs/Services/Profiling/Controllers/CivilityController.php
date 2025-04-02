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
    public function Get(): ?EntityCollectable
    {
        $arr = [
            Civility::class => $this->service->GetCivilities(),
            Gender::class => $this->service->GetGenders(),
            Occupation::class => $this->service->GetOccupations(),
            Title::class => $this->service->GetTitles(),
            Status::class => $this->service->GetStatuses()
        ];
        return new EntityCollectable($arr);
    }

    public function GetAll(string $type): ?EntityCollectable
    {
        return match ($type){
            Civility::class => $this->service->GetCivilities(),
            Gender::class => $this->service->GetGenders(),
            Occupation::class => $this->service->GetOccupations(),
            Title::class => $this->service->GetTitles(),
            Status::class => $this->service->GetStatuses()
        };
    }
}