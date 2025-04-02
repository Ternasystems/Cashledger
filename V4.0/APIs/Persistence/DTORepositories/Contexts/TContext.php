<?php

namespace API_DTORepositories_Context;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Model\DTOBase;
use Exception;
use TS_Database\Traits\TraitContext;

trait TContext
{
    use TraitContext;

    protected function SetStatement(string $entityName = DTOBase::class, ?array $args = null): string
    {
        $backtrace = debug_backtrace();
        $procName = 'p_'.$backtrace[1]['function'].explode('\\', $entityName)[1];
        $str = !is_null($args) ? implode(', ', array_fill(0, count($args), '?')) : null;
        return sprintf('CALL "%s"(%s)', $procName, $str);
    }

    /**
     * @throws Exception
     */
    public function SelectAll(string $entityName = DTOBase::class): array
    {
        if (!property_exists($this, $entityName))
            throw new Exception("Property $entityName does not exist");

        $sql = sprintf('SELECT * FROM "%s" WHERE "IsActive" IS NULL', $this->{$entityName});
        $this->GetSelectQuery()->SetStatement($sql);
        return $this->GetSelectQuery()->QueryPDO($this->pdo, null, 1);
    }

    /**
     * @throws Exception
     */
    public function SelectById(string $Id, string $entityName = DTOBase::class): array{
        if (!property_exists($this, $entityName))
            throw new Exception("Property $entityName does not exist");

        $sql = sprintf('SELECT * FROM "%s" WHERE "ID" = ? AND "IsActive" IS NULL', $this->{$entityName});
        $this->GetSelectQuery()->SetStatement($sql);
        return $this->GetSelectQuery()->QueryPDO($this->pdo, array($Id), 1);
    }

    public function Insert(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $sql = $this->SetStatement($entityName, $args);
        $this->Query($sql, $args);
    }

    public function Update(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $sql = $this->SetStatement($entityName, $args);
        $this->Query($sql, $args);
    }

    public function Delete(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $sql = $this->SetStatement($entityName, $args);
        $this->Query($sql, $args);
    }

    public function Disable(string $entityName = DTOBase::class, ?array $args = null): void
    {
        $sql = $this->SetStatement($entityName, $args);
        $this->Query($sql, $args);
    }

    public function Mapping(string $entityName, array $data): ?DTOBase
    {
        $entity = parent::Mapping($entityName, $data);
        return $entity instanceof DTOBase ? $entity : null;
    }

    /**
     * @throws Exception
     */
    public function MappingCollection(string $entityName, array $data): ?Collectable
    {
        if (!isset($this->entityMap[$entityName]))
            throw new Exception('Not a valid entity name: ' . $entityName);

        $entity = $this->entityMap[$entityName];
        if (!class_exists($entity))
            throw new Exception('Not a valid class: ' . $entity);

        return new $entity($data);
    }
}