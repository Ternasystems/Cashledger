<?php

namespace API_ProfilingRepositories_Collection;

use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Model\Token;

class Tokens extends Collectable
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