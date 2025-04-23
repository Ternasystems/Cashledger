<?php

namespace APP_Inventory_Model;

use DateTime;

class CustomerModel
{
    // Profile data
    public ?string $profileid;
    public ?string $firstname;
    public ?string $maidenname;
    public string $lastname;
    public DateTime $birthdate;
    public array $civilities;
    public ?string $photo;

    // Contact data

    // Customer data
    public ?string $customerid;
    public ?string $desc;
}