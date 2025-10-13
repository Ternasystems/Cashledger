<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Collection\Cities;
use API_DTOEntities_Collection\Countries;
use API_DTOEntities_Factory\CityFactory;
use API_DTOEntities_Factory\CollectableFactory;
use API_DTOEntities_Factory\CountryFactory;
use API_ProfilingEntities_Collection\Civilities;
use API_ProfilingEntities_Collection\Contacts;
use API_ProfilingEntities_Collection\Genders;
use API_ProfilingEntities_Collection\Occupations;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Collection\Statuses;
use API_ProfilingEntities_Collection\Titles;
use API_ProfilingEntities_Model\Profile;
use API_ProfilingRepositories\ProfileRepository;
use API_RelationRepositories\LanguageRelationRepository;
use TS_Exception\Classes\DomainException;

class ProfileFactory extends CollectableFactory
{
    private CivilityFactory $civilityFactory;
    private Civilities $civilities;
    private GenderFactory $genderFactory;
    private Genders $genders;
    private OccupationFactory $occupationFactory;
    private Occupations $occupations;
    private StatusFactory $statusFactory;
    private Statuses $statuses;
    private TitleFactory $titleFactory;
    private Titles $titles;
    private ContactFactory $contactFactory;
    private Contacts $contacts;
    private CountryFactory $countryFactory;
    private Countries $countries;
    private CityFactory $cityFactory;
    private Cities $cities;

    public function __construct(ProfileRepository $repository, CivilityFactory $civilityFactory, GenderFactory $genderFactory, OccupationFactory $occupationFactory,
                                StatusFactory $statusFactory, TitleFactory $titleFactory, ContactFactory $contactFactory, CountryFactory $countryFactory, CityFactory $cityFactory,
                                LanguageRelationRepository $languageRelationRepository)
    {
        parent::__construct($repository, $languageRelationRepository);
        $this->civilityFactory = $civilityFactory;
        $this->genderFactory = $genderFactory;
        $this->occupationFactory = $occupationFactory;
        $this->statusFactory = $statusFactory;
        $this->titleFactory = $titleFactory;
        $this->contactFactory = $contactFactory;
        $this->countryFactory = $countryFactory;
        $this->cityFactory = $cityFactory;
    }

    /**
     * @throws DomainException
     */
    protected function fetchDependencies(): void
    {
        $this->collection = $this->repository()->getBy($this->whereClause, $this->limit, $this->offset);

        $this->civilityFactory->Create();
        $this->civilities = $this->civilityFactory->collectable();
        //
        $this->genderFactory->Create();
        $this->genders = $this->genderFactory->collectable();
        //
        $this->occupationFactory->Create();
        $this->occupations = $this->occupationFactory->collectable();
        //
        $this->statusFactory->Create();
        $this->statuses = $this->statusFactory->collectable();
        //
        $this->titleFactory->Create();
        $this->titles = $this->titleFactory->collectable();
        //
        $this->contactFactory->Create();
        $this->contacts = $this->contactFactory->collectable();
        //
        $this->countryFactory->Create();
        $this->countries = $this->countryFactory->collectable();
        //
        $this->cityFactory->Create();
        $this->cities = $this->cityFactory->collectable();
    }

    /**
     * @throws DomainException
     */
    protected function build(): void
    {
        $profiles = [];
        if ($this->collection)
            $profiles = $this->collection->select(fn($n) => new Profile($n, $this->civilities[$n->CivilityID], $this->genders[$n->GenderID], $this->occupations[$n->OccupationID],
            $this->statuses[$n->StatusID], $this->titles[$n->TitleID], $this->contacts->where(fn($n) => $n->it()->ProfileId == $n->Id), $this->countries[$n->CountryID],
            $this->cities[$n->CityID]))->toArray();

        $this->collectable = new Profiles($profiles);
    }
}