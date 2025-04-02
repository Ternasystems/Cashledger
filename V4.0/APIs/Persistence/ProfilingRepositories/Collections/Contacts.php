<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Contact;

class Contacts extends Collectable
{
    public function __construct(array $collection, string $objectType = Contact::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Contact
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Contact ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Contact
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Contact ? $entity : null;
    }
}