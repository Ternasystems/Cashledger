<?php

namespace API_Profiling_Controller;

use API_Profiling_Contract\IContactService;
use API_ProfilingEntities_Collection\Contacts;
use API_ProfilingEntities_Collection\ContactTypes;
use API_ProfilingEntities_Model\Contact;
use API_ProfilingEntities_Model\ContactType;
use TS_Controller\Classes\BaseController;

class ContactController extends BaseController
{
    protected IContactService $service;

    public function __construct(IContactService $_service)
    {
        $this->service = $_service;
    }

    public function Get(): ?Contacts
    {
        return $this->service->GetContacts();
    }

    public function GetById(string $id): ?Contact
    {
        return $this->service->GetContacts(fn($n) => $n->It()->Id == $id);
    }

    public function GetByType(string $contactTypeId): ?Contacts
    {
        return $this->service->GetContacts(fn($n) => $n->It()->ContactTypeId == $contactTypeId);
    }

    public function GetTypes(): ?ContactTypes
    {
        return $this->service->GetContactTypes();
    }

    public function GetTypesById(string $id): ?ContactType
    {
        return $this->service->GetContactTypes(fn($n) => $n->It()->Id == $id);
    }
}