<?php

namespace API_ProfilingEntities_Model;

use API_DTOEntities_Model\City;
use API_DTOEntities_Model\Country;
use API_DTOEntities_Model\Entity;
use API_ProfilingEntities_Collection\Contacts;
use UnexpectedValueException;

class Profile extends Entity
{
    private array $fullName;
    private Civility $civility;
    private Gender $gender;
    private Occupation $occupation;
    private Title $title;
    private Status $status;
    private Contacts $contacts;
    private Country $country;
    private City $city;

    public function __construct(\API_ProfilingRepositories_Model\Profile $_entity, Contacts $_contacts, Civility $_civility, Gender $_gender, Occupation $_occupation, Title $_title,
                                Status $_status, Country $_country, City $_city)
    {
        parent::__construct($_entity, null);
        $this->fullName = [
            'LastName' => $_entity->LastName,
            'MaidenName' => $_entity->MaidenName,
            'FirstName' => $_entity->FirstName
        ];
        $this->civility = $_civility;
        $this->gender = $_gender;
        $this->occupation = $_occupation;
        $this->title = $_title;
        $this->status = $_status;
        $this->country = $_country;
        $this->city = $_city;
        $this->contacts = $_contacts->Where(fn($n) => $n->It()->ProfileId == $_entity->Id);
    }

    public function It(): \API_ProfilingRepositories_Model\Profile
    {
        $entity = parent::It();
        if (!$entity instanceof \API_ProfilingRepositories_Model\Profile)
            throw new UnexpectedValueException('Object must be an instance of '.\API_ProfilingRepositories_Model\Profile::class);

        return $entity;
    }

    public function FullName(): array
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

    public function Title(): Title
    {
        return $this->title;
    }

    public function Status(): Status
    {
        return $this->status;
    }

    public function Country(): Country
    {
        return $this->country;
    }

    public function City(): City
    {
        return $this->city;
    }

    public function Contacts(): Contacts
    {
        return $this->contacts;
    }
}