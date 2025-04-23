<?php

namespace API_Profiling_Controller;

use API_Profiling_Contract\IProfileService;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Model\Profile;
use TS_Controller\Classes\BaseController;

class ProfileController extends BaseController
{
    private IProfileService $service;

    public function __construct(IProfileService $_service)
    {
        $this->service = $_service;
    }

    public function Get(): ?Profiles
    {
        return $this->service->GetProfiles();
    }

    public function GetById(string $id): ?Profile
    {
        return $this->service->GetProfiles(fn($n) => $n->It()->Id == $id);
    }

    public function GetByName(string $lastName): ?Profiles
    {
        return $this->service->GetProfiles(fn($n) => $n->It()->LastName == $lastName);
    }

    public function Set(object $profile): void
    {
        $this->service->SetProfile($profile);
    }

    public function Put(object $profile): void
    {
        $this->service->PutProfile($profile);
    }

    public function Delete(string $id): void
    {
        $this->service->DeleteProfile($id);
    }
}