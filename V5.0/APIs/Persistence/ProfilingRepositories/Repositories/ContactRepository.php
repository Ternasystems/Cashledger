<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Contacts;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Contact;
use Closure;

class ContactRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Contact
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?Contacts
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Contacts ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Contact
    {
        $entity = parent::last($predicate);
        return $entity instanceof Contact ? $entity : null;
    }
}