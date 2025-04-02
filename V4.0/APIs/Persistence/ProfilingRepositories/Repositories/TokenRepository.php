<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Context\TokenContext;
use API_ProfilingRepositories_Collection\Tokens;
use API_ProfilingRepositories_Model\Token;
use Exception;
use TS_Database\Traits\TraitContext;
use TS_Utility\Enums\OrderEnum;

class TokenRepository extends Repository
{
    private ?string $roleName = null;

    public function __construct(TokenContext $context)
    {
        parent::__construct($context);
    }

    use TraitContext;

    protected function SetStatement(string $entityName = Token::class, ?array $args = null): string
    {
        $backtrace = debug_backtrace();
        $function = match ($backtrace[1]['function']){
            'Add' => 'Insert',
            'Remove' => 'Delete',
            'Update' => 'Update'
        };

        $procName = 'p_'.$function.'Role_'.$this->roleName;
        $str = !is_null($args) ? implode(', ', array_fill(0, count($args), '?')) : null;
        return sprintf('CALL "%s"(%s)', $procName, $str);
    }

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
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Token ? $entity : null;
    }

    public function GetAll(): ?Tokens
    {
        $entityName = $this->GetEntityName('GetById');
        $entityName = strtolower(explode('\\', $entityName)[1]);

        $tableName = 'cl_Role_'.$this->roleName;
        $data = $this->context->SelectAll($tableName);

        if (empty($data))
            return null;

        $objectArray = [];
        foreach ($data as $item)
            $objectArray[] = $this->context->Mapping($entityName, $item);

        return $this->context->MappingCollection($entityName.'collection', $objectArray);
    }

    public function GetById(string $id): ?Token
    {
        $entityName = $this->GetEntityName('GetById');
        $entityName = strtolower(explode('\\', $entityName)[1]);

        $tableName = 'cl_Role_'.$this->roleName;
        $data = $this->context->SelectById($id, $tableName);

        if (empty($data))
            return null;

        return $this->context->Mapping($entityName, $data[0]);
    }

    public function GetBy(callable $predicate): ?Tokens
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Tokens ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Token
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Token ? $entity : null;
    }

    public function Add(string $entityName = Token::class, ?array $args = null): void
    {
        $sql = $this->SetStatement($entityName, $args);
        $this->Query($sql, $args);
    }

    public function Remove(string $entityName = Token::class, ?array $args = null): void
    {
        $sql = $this->SetStatement($entityName, $args);
        $this->Query($sql, $args);
    }

    public function Update(string $entityName = Token::class, ?array $args = null): void
    {
        $sql = $this->SetStatement($entityName, $args);
        $this->Query($sql, $args);
    }

    public function OrderBy(Collectable $tokens, array $properties, array $orderBy = [OrderEnum::ASC]): ?Tokens
    {
        if (!$tokens instanceof Tokens)
            throw new Exception("$tokens must be instance of Tokens");

        $collection = parent::OrderBy($tokens, $properties, $orderBy);
        return $collection instanceof Tokens ? $collection : null;
    }
}