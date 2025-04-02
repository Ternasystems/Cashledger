<?php

namespace API_ProfilingEntities_Collection;

use API_DTOEntities_Collection\EntityCollectable;
use API_ProfilingEntities_Model\Token;

class Tokens extends EntityCollectable
{
    public function __construct(array $collection, string $objectType = Token::class, string $keySet = null)
    {
        parent::__construct($collection, $objectType, $keySet);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Token
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Token ? $entity : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Token
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Token ? $entity : null;
    }
}