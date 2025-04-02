<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Contact;

class Contacts extends EntityCollectable
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