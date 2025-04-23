<?php

namespace API_Profiling_Service;

use API_Profiling_Contract\ICivilityService;
use API_Profiling_Contract\IContactService;
use API_Profiling_Contract\IProfileService;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Factory\ProfileFactory;
use API_ProfilingEntities_Model\Profile;
use API_ProfilingRepositories_Model\Civility;
use API_ProfilingRepositories_Model\Gender;
use API_ProfilingRepositories_Model\Occupation;
use API_ProfilingRepositories_Model\Status;
use API_ProfilingRepositories_Model\Title;
use Exception;
use ReflectionException;

class ProfileService implements IProfileService
{
    protected ProfileFactory $profileFactory;
    protected ICivilityService $civilityService;
    protected IContactService $contactService;

    /**
     * @throws Exception
     */
    public function __construct(ProfileFactory $_profileFactory, ICivilityService $_civilityService, IContactService $_contactService)
    {
        $this->profileFactory = $_profileFactory;
        $this->civilityService = $_civilityService;
        $this->contactService = $_contactService;
    }

    /**
     * @throws Exception
     */
    public function GetProfiles(callable $predicate = null): Profile|Profiles|null
    {
        $this->profileFactory->Create();

        if (is_null($predicate))
            return $this->profileFactory->Collectable();

        $collection = $this->profileFactory->Collectable()->Where($predicate);

        if ($collection->Count() == 0)
            return null;

        return $collection->Count() > 1 ? $collection : $collection->first();
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function SetProfile(object $model): void
    {
        $repository = $this->profileFactory->Repository();
        $repository->Add(\API_ProfilingRepositories_Model\Profile::class, array($model->lastname, $model->birthdate, $model->firstname, $model->maidenname, $model->photo,
            $model->desc));
        $this->profileFactory->Create();
        $id = $this->profileFactory->Collectable()->FirstOrDefault(fn($n) => $n->It()->LastName == $model->lastname && $n->Birthdate == $model->birthdate)->It()->Id;
        //
        $civilities = $this->civilityService->GetRelationRepositories();
        $civilities['Civilities']->Add(Civility::class, array($model->civilities['CivilityId'], $id));
        $civilities['Genders']->Add(Gender::class, array($model->civilities['GenderId'], $id));
        $civilities['Occupations']->Add(Occupation::class, array($model->civilities['OccupationId'], $id));
        $civilities['Statuses']->Add(Status::class, array($model->civilities['StatusId'], $id));
        $civilities['Titles']->Add(Title::class, array($model->civilities['TitleId'], $id));
    }

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function PutProfile(object $model): void
    {
        $repository = $this->profileFactory->Repository();
        $repository->Update(\API_ProfilingRepositories_Model\Profile::class, array($model->profileid, $model->lastname, $model->birthdate, $model->maidenname,
            $model->firstname, $model->photo, $model->desc));
        $this->profileFactory->Create();
        $civilities = $this->civilityService->GetRelationRepositories();
        //
        $id = $civilities['Civilities']->GetBy(fn($n) => $n->ProfileId == $model->profileid)->FirstOrDefault()->Id;
        $civilities['Civilities']->Remove(Civility::class, array($id));
        //
        $id = $civilities['Genders']->GetBy(fn($n) => $n->ProfileId == $model->profileid)->FirstOrDefault()->Id;
        $civilities['Genders']->Remove(Gender::class, array($id));
        //
        $id = $civilities['Occupations']->GetBy(fn($n) => $n->ProfileId == $model->profileid)->FirstOrDefault()->Id;
        $civilities['Occupations']->Remove(Occupation::class, array($id));
        //
        $id = $civilities['Statuses']->GetBy(fn($n) => $n->ProfileId == $model->profileid)->FirstOrDefault()->Id;
        $civilities['Statuses']->Remove(Status::class, array($id));
        //
        $id = $civilities['Titles']->GetBy(fn($n) => $n->ProfileId == $model->profileid)->FirstOrDefault()->Id;
        $civilities['Titles']->Remove(Title::class, array($id));
        //
        $civilities['Civilities']->Add(Civility::class, array($model->civilities['CivilityId'], $model->profileid));
        $civilities['Genders']->Add(Gender::class, array($model->civilities['GenderId'], $model->profileid));
        $civilities['Occupations']->Add(Occupation::class, array($model->civilities['OccupationId'], $model->profileid));
        $civilities['Statuses']->Add(Status::class, array($model->civilities['StatusId'], $model->profileid));
        $civilities['Titles']->Add(Title::class, array($model->civilities['TitleId'], $model->profileid));
    }

    /**
     * @throws Exception
     */
    public function DeleteProfile(string $id): void
    {
        $this->profileFactory->Create();
        $civilities = $this->civilityService->GetRelationRepositories();
        //
        $itemId = $civilities['Civilities']->GetBy(fn($n) => $n->ProfileId == $id)->FirstOrDefault()->Id;
        $civilities['Civilities']->Remove(Civility::class, array($itemId));
        //
        $itemId = $civilities['Genders']->GetBy(fn($n) => $n->ProfileId == $id)->FirstOrDefault()->Id;
        $civilities['Genders']->Remove(Gender::class, array($itemId));
        //
        $itemId = $civilities['Occupations']->GetBy(fn($n) => $n->ProfileId == $id)->FirstOrDefault()->Id;
        $civilities['Occupations']->Remove(Occupation::class, array($itemId));
        //
        $itemId = $civilities['Statuses']->GetBy(fn($n) => $n->ProfileId == $id)->FirstOrDefault()->Id;
        $civilities['Statuses']->Remove(Status::class, array($itemId));
        //
        $itemId = $civilities['Titles']->GetBy(fn($n) => $n->ProfileId == $id)->FirstOrDefault()->Id;
        $civilities['Titles']->Remove(Title::class, array($itemId));
        //
        $repository = $this->profileFactory->Repository();
        $repository->Remove(\API_ProfilingRepositories_Model\Profile::class, array($id));
    }
}