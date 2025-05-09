<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CityFactory;
use API_DTOEntities_Factory\CollectableFactory;
use API_DTOEntities_Factory\CountryFactory;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Model\Profile;
use API_ProfilingRepositories\ProfileRepository;
use Exception;
use ReflectionException;

class ProfileFactory extends CollectableFactory
{
    protected ContactFactory $contactFactory;
    protected CivilityFactory $civilityFactory;
    protected GenderFactory $genderFactory;
    protected OccupationFactory $occupationFactory;
    protected TitleFactory $titleFactory;
    protected StatusFactory $statusFactory;
    protected CountryFactory $countryFactory;
    protected CityFactory $cityFactory;

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function __construct(ProfileRepository $repository, ContactFactory $_contactFactory, CivilityFactory $_civilityFactory, GenderFactory $_genderFactory,
                                OccupationFactory $_occupationFactory, TitleFactory $_titleFactory, StatusFactory $_statusFactory, CountryFactory $_countryFactory,
                                CityFactory $_cityFactory)
    {
        parent::__construct($repository, null);
        $this->contactFactory = $_contactFactory;
        $this->civilityFactory = $_civilityFactory;
        $this->genderFactory = $_genderFactory;
        $this->occupationFactory = $_occupationFactory;
        $this->titleFactory = $_titleFactory;
        $this->statusFactory = $_statusFactory;
        $this->countryFactory = $_countryFactory;
        $this->cityFactory = $_cityFactory;
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $this->contactFactory->Create();
        $contacts = $this->contactFactory->Collectable();
        $this->civilityFactory->Create();
        $civilities = $this->civilityFactory->Collectable();
        $this->genderFactory->Create();
        $genders = $this->genderFactory->Collectable();
        $this->occupationFactory->Create();
        $occupations = $this->occupationFactory->Collectable();
        $this->titleFactory->Create();
        $titles = $this->titleFactory->Collectable();
        $this->statusFactory->Create();
        $statuses = $this->statusFactory->Collectable();
        $this->countryFactory->Create();
        $countries = $this->countryFactory->Collectable();
        $this->cityFactory->Create();
        $cities = $this->cityFactory->Collectable();
        //
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item) {
            $civility = $civilities->FirstOrDefault(fn($n) => $n->CivilityRelations()->Any(fn($t) => $t->ProfileId == $item->Id));
            $gender = $genders->FirstOrDefault(fn($n) => $n->GenderRelations()->Any(fn($t) => $t->ProfileId == $item->Id));
            $occupation = $occupations->FirstOrDefault(fn($n) => $n->OccupationRelations()->Any(fn($t) => $t->ProfileId == $item->Id));
            $title = $titles->FirstOrDefault(fn($n) => $n->TitleRelations()->Any(fn($t) => $t->ProfileId == $item->Id));
            $status = $statuses->FirstOrDefault(fn($n) => $n->StatusRelations()->Any(fn($t) => $t->ProfileId == $item->Id));
            $country = $countries->FirstOrDefault(fn($n) => $n->It()->Id == $item->CountryId);
            $city = $cities->FirstOrDefault(fn($n) => $n->It()->Id == $item->CityId);
            $colArray[] = new Profile($item, $contacts, $civility, $gender, $occupation, $title, $status, $country, $city);
        }

        $this->collectable = new Profiles($colArray);
    }

    public function Collectable(): ?Profiles
    {
        return $this->collectable;
    }

    public function Repository(): ProfileRepository
    {
        return $this->repository;
    }
}