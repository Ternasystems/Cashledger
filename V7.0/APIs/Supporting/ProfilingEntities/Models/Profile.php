<?php

namespace API_ProfilingEntities_Model;

use API_Assets\Classes\DTOException;
use API_DTOEntities_Model\City;
use API_DTOEntities_Model\Country;
use API_DTOEntities_Model\Entity;
use API_ProfilingEntities_Collection\Contacts;

class Profile extends Entity
{
    private array $fullName;
    private Civility $civility;
    private Gender $gender;
    private Occupation $occupation;
    private Status $status;
    private Title $title;
    private Contacts $contacts;
    private Country $country;
    private City $city;

    public function __construct(\API_ProfilingRepositories_Model\Profile $_entity, Civility $_civility, Gender $_gender, Occupation $_occupation, Status $_status, Title $_title,
                                Contacts $_contacts, Country $_country, City $_city)
    {
        parent::__construct($_entity);
        $this->fullName = ['LastName' => $_entity->LastName, 'MaidenName' => $_entity->MaidenName, 'FirstName' => $_entity->FirstName];
        $this->civility = $_civility;
        $this->gender = $_gender;
        $this->occupation = $_occupation;
        $this->status = $_status;
        $this->title = $_title;
        $this->country = $_country;
        $this->city = $_city;
        $this->contacts = $_contacts->where(fn($n) => $n->it()->ProfileId == $_entity->Id);
    }

    /**
     * @throws DTOException
     */
    public function it(): \API_ProfilingRepositories_Model\Profile
    {
        $entity = parent::it();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Profile) {
            throw new DTOException('invalid_entity_name', [':name' => \API_ProfilingRepositories_Model\Profile::class]);
        }

        return $entity;
    }

    public function fullName(): array
    {
        return $this->fullName;
    }

    public function Civility(): Civility
    {
        return $this->civility;
    }

    public function Gender(): Gender
    {
        return $this->gender;
    }

    public function Occupation(): Occupation
    {
        return $this->occupation;
    }

    public function Status(): Status
    {
        return $this->status;
    }

    public function Title(): Title
    {
        return $this->title;
    }

    public function Contacts(): Contacts
    {
        return $this->contacts;
    }

    public function Country(): Country
    {
        return $this->country;
    }

    public function City(): City
    {
        return $this->city;
    }
}