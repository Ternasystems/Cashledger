<?php

namespace API_Profiling_Contract;

use API_ProfilingEntities_Collection\Contacts;
use API_ProfilingEntities_Collection\ContactTypes;
use API_ProfilingEntities_Model\Contact;
use API_ProfilingEntities_Model\ContactType;

interface IContactService
{
    public function GetContacts(callable $predicate = null): Contact|Contacts|null;
    public function GetContactTypes(callable $predicate = null): ContactType|ContactTypes|null;
}