<?php

namespace API_ProfilingEntities_Factory;

use API_DTOEntities_Factory\CollectableFactory;
use API_ProfilingEntities_Collection\Civilities;
use API_ProfilingEntities_Collection\Contacts;
use API_ProfilingEntities_Collection\Genders;
use API_ProfilingEntities_Collection\Occupations;
use API_ProfilingEntities_Collection\Profiles;
use API_ProfilingEntities_Collection\Statuses;
use API_ProfilingEntities_Collection\Titles;
use API_ProfilingEntities_Model\Profile;
use API_ProfilingRepositories\ProfileRepository;
use Exception;
use ReflectionException;

class ProfileFactory extends CollectableFactory
{
    protected Contacts $contacts;
    protected Civilities $civilities;
    protected Genders $genders;
    protected Occupations $occupations;
    protected Titles $titles;
    protected Statuses $statuses;

    /**
     * @throws ReflectionException
     * @throws Exception
     */
    public function __construct(ProfileRepository $repository, ContactFactory $_contactFactory, CivilityFactory $_civilityFactory, GenderFactory $_genderFactory,
                                OccupationFactory $_occupationFactory, TitleFactory $_titleFactory, StatusFactory $_statusFactory)
    {
        parent::__construct($repository, null);

        $_contactFactory->Create();
        $this->contacts = $_contactFactory->Collectable();
        $_civilityFactory->Create();
        $this->civilities = $_civilityFactory->Collectable();
        $_genderFactory->Create();
        $this->genders = $_genderFactory->Collectable();
        $_occupationFactory->Create();
        $this->occupations = $_occupationFactory->Collectable();
        $_titleFactory->Create();
        $this->titles = $_titleFactory->Collectable();
        $_statusFactory->Create();
        $this->statuses = $_statusFactory->Collectable();
    }

    /**
     * @throws Exception
     */
    public function Create(): void
    {
        $collection = $this->repository->GetAll();
        $colArray = [];
        foreach ($collection as $item) {
            $civility = $this->civilities->FirstOrDefault(fn($n) => $n->CivilityRelations()->Where(fn($t) => $t->ProfileId == $item->Id));
            $gender = $this->genders->FirstOrDefault(fn($n) => $n->GenderRelations()->Where(fn($t) => $t->ProfileId == $item->Id));
            $occupation = $this->occupations->FirstOrDefault(fn($n) => $n->OccupationRelations()->Where(fn($t) => $t->ProfileId == $item->Id));
            $title = $this->titles->FirstOrDefault(fn($n) => $n->TitleRelations()->Where(fn($t) => $t->ProfileId == $item->Id));
            $status = $this->statuses->FirstOrDefault(fn($n) => $n->StatusRelations()->Where(fn($t) => $t->ProfileId == $item->Id));
            $colArray[] = new Profile($item, $this->contacts, $civility, $gender, $occupation, $title, $status);
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