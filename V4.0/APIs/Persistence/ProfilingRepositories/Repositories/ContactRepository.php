<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Collection\Contacts;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Contact;
use Exception;
use TS_Utility\Enums\OrderEnum;

class ContactRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Contact
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Contact ? $entity : null;
    }

    public function GetAll(): ?Contacts
    {
        $collection = parent::GetAll();
        return $collection instanceof Contacts ? $collection : null;
    }

    public function GetById(string $id): ?Contact
    {
        $entity = parent::GetById($id);
        return $entity instanceof Contact ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Contacts
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Contacts ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Contact
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Contact ? $entity : null;
    }

    public function OrderBy(Collectable $contacts, array $properties, array $orderBy = [OrderEnum::ASC]): ?Contacts
    {
        if (!$contacts instanceof Contacts)
            throw new Exception("Contacts must be instance of Contacts");

        $collection = parent::OrderBy($contacts, $properties, $orderBy);
        return $collection instanceof Contacts ? $collection : null;
    }
}