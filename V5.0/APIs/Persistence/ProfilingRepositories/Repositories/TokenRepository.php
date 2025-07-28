<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Context\TContext;
use API_ProfilingRepositories_Collection\Tokens;
use API_ProfilingRepositories_Context\TokenContext;
use API_ProfilingRepositories_Model\Token;
use Closure;

class TokenRepository extends Repository
{
    private ?string $roleName = null;

    public function __construct(TokenContext $context)
    {
        parent::__construct($context);
    }

    use TContext;

    public function SetTableName(string $_roleName): void
    {
        $this->roleName = $_roleName;
    }

    public function GetTableName(): string
    {
        return $this->roleName;
    }

    public function FirstOrDefault(?callable $predicate = null): ?Token
    {
        $entity = parent::first($predicate);
        return $entity instanceof Token ? $entity : null;
    }

    public function GetAll(): ?Tokens
    {
        $collection = parent::GetAll();
        return $collection instanceof Tokens ? $collection : null;
    }

    public function GetById(string $id): ?Token
    {
        $entity = parent::GetById($id);
        return $entity instanceof Token ? $entity : null;
    }

    public function GetBy(Closure $predicate): ?Tokens
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Tokens ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Token
    {
        $entity = parent::last($predicate);
        return $entity instanceof Token ? $entity : null;
    }

    public function add(array $args): void
    {
        $this->context->Insert($this->roleName, $args);
    }

    public function remove(string $id): void
    {
        $this->context->Delete($this->roleName, [$id]);
    }

    public function deactivate(string $id): void
    {
        $this->context->Disable($this->roleName, [$id]);
    }

    public function update(array $args): void
    {
        $this->context->Update($this->roleName, $args);
    }
}