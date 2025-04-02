<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the Jéoline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Database\Classes;

use DateTime;
use Exception;
use PDO;
use ReflectionClass;
use ReflectionProperty;
use TS_Configuration\Classes\AbstractCls;
use TS_Exception\Classes\DBException;

header('Content-Type: text/html; charset=utf-8');

/*
 * DBContext class
 */
class DBContext extends AbstractCls
{
    private static ?DBContext $instance = null;
    private DBConnection $pdo;
    protected ?DBSelect $selectQuery = null;
    protected ?DBUpdate $updateQuery = null;
    protected ?DBDelete $deleteQuery = null;
    protected ?DBProc $procQuery = null;
    protected ?DBQuery $execQuery = null;
    protected array $entityMap = [];
    protected array $propertyMap = [];

    private function __construct(array $_connectionString)
    {
        $this->pdo = new DBConnection();
        DBConnection::PDOConnection($_connectionString[0], $_connectionString[1], $_connectionString[2]);
    }

    /* Inherited protected methods */

    // Method to set the exception property
    protected function setException(): void
    {
        $this->exception = new DBException();
    }

    // Method to get the exception property
    public function getException(): void
    {
        throw $this->exception;
    }

    public static function GetConnection(array $_connectionString): PDO
    {
        if (self::$instance == null) self::$instance = new DBContext($_connectionString);
        return self::$instance->pdo->GetPDO();
    }

    /**
     * @throws Exception
     */
    public function Mapping(string $entityName, array $data): ?object
    {
        if (!isset($this->entityMap[$entityName]))
            throw new Exception('Not a valid entity name: ' . $entityName);

        $entity = $this->entityMap[$entityName];
        if (!class_exists($entity))
            throw new Exception('Not a valid class: ' . $entity);

        $instance = new $entity();
        $class = new ReflectionClass($entity);

        foreach ($data as $key => $value) {
            $property = property_exists($instance, $key) ? $key : $this->propertyMap[$key];
            $instance->{$property} = $this->TypeCasting($class->getProperty($property), $value);
        }

        return $instance;
    }

    /**
     * @throws DBException
     */
    public function GetStatement(): string
    {
        if ($this->selectQuery != null)
            return $this->selectQuery->GetStatement();
        elseif ($this->updateQuery != null)
            return $this->updateQuery->GetStatement();
        elseif ($this->deleteQuery != null)
            return $this->deleteQuery->GetStatement();
        elseif ($this->procQuery != null)
            return $this->procQuery->GetStatement();
        elseif ($this->execQuery != null)
            return $this->execQuery->GetStatement();
        else
            throw new DBException('Query does not match any predefined SELECT, UPDATE, DELETE, PROC or EXECUTE templates');
    }

    /**
     * @throws Exception
     */
    protected function TypeCasting(ReflectionProperty $property, mixed $value): mixed
    {
        $propertyType = $property->getType();
        $typeName = $propertyType?->getName();

        return is_null($value) ? null : match ($typeName){
            'string' => (string)$value,
            'int' => (int)$value,
            'bool' => (bool)$value,
            'float', 'double' => (float)$value,
            'DateTime' => is_string($value) ? new DateTime($value) : null,
            default => $value
        };
    }

    protected function GetSelectQuery(): DBSelect
    {
        return $this->selectQuery ?? $this->selectQuery = new DBSelect();
    }

    protected function GetUpdateQuery(): DBUpdate
    {
        return $this->updateQuery ?? $this->updateQuery = new DBUpdate();
    }

    protected function GetDeleteQuery(): DBDelete
    {
        return $this->deleteQuery ?? $this->deleteQuery = new DBDelete();
    }

    protected function GetProcQuery(): DBProc
    {
        return $this->procQuery ?? $this->procQuery = new DBProc();
    }

    protected function GetExecQuery(): DBQuery
    {
        return $this->execQuery ?? $this->execQuery = new DBQuery();
    }
}