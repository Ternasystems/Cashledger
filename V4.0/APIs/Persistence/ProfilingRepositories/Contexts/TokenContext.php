<?php

namespace API_ProfilingRepositories_Context;

use API_DTORepositories_Collection\Collectable;
use API_DTORepositories_Model\DTOBase;
use API_ProfilingRepositories_Collection\Tokens;
use API_ProfilingRepositories_Model\Token;
use Exception;
use PDO;
use TS_Database\Classes\DBContext;
use TS_Database\Traits\TraitContext;

class TokenContext extends DBContext
{
    protected PDO $pdo;

    public function __construct(array $_connectionString){
        $this->pdo = DBContext::GetConnection($_connectionString);
        $this->SetEntityMap();
        $this->SetPropertyMap();
    }

    use TraitContext;

    private function SetEntityMap(): void
    {
        $this->entityMap = [
            'token' => Token::class,
            'tokencollection' => Tokens::class
        ];
    }

    private function SetPropertyMap(): void
    {
        $this->propertyMap = [
            'ID' => 'Id',
            'RoleID' => 'RoleId'
        ];
    }

    public function SelectAll(string $entityName = DTOBase::class): array
    {
        $sql = sprintf('SELECT * FROM "%s" WHERE "IsActive" IS NULL', $entityName);
        $this->GetSelectQuery()->SetStatement($sql);
        return $this->GetSelectQuery()->QueryPDO($this->pdo, null, 1);
    }

    public function SelectById(string $Id, string $entityName = DTOBase::class): array
    {
        $sql = sprintf('SELECT * FROM "%s" WHERE "ID" = ? AND "IsActive" IS NULL', $entityName);
        $this->GetSelectQuery()->SetStatement($sql);
        return $this->GetSelectQuery()->QueryPDO($this->pdo, array($Id), 1);
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