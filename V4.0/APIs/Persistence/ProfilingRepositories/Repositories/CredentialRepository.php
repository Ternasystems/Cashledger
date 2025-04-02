<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_DTORepositories_Collection\Collectable;
use API_ProfilingRepositories_Collection\Credentials;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Credential;
use Exception;
use PDO;
use TS_Exception\Classes\DBException;
use TS_Utility\Enums\OrderEnum;

class CredentialRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Credential
    {
        $entity = parent::FirstOrDefault($predicate);
        return $entity instanceof Credential ? $entity : null;
    }

    public function GetAll(): ?Credentials
    {
        $collection = parent::GetAll();
        return $collection instanceof Credentials ? $collection : null;
    }

    public function GetById(string $id): ?Credential
    {
        $entity = parent::GetById($id);
        return $entity instanceof Credential ? $entity : null;
    }

    public function GetBy(callable $predicate): ?Credentials
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Credentials ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Credential
    {
        $entity = parent::LastOrDefault($predicate);
        return $entity instanceof Credential ? $entity : null;
    }

    /**
     * @throws Exception
     */
    public function OrderBy(Collectable $credentials, array $properties, array $orderBy = [OrderEnum::ASC]): ?Credentials
    {
        if (!$credentials instanceof Credentials)
            throw new Exception("Credentials must be instance of Credentials");

        $collection = parent::OrderBy($credentials, $properties, $orderBy);
        return $collection instanceof Credentials ? $collection : null;
    }

    public function UpdatePassword(string $credentialId, string $oldPassword, string $newPassword): void
    {
        $sql = 'CALL "p_UpdatePassword"(:credentialId, :oldPassword, :newPassword)';
        $args = [
            ':credentialId' => $credentialId,
            ':oldPassword' => $oldPassword,
            ':newPassword' => $newPassword
        ];
        $this->context->ExecuteQuery($sql, $args);
    }

    public function ResetPassword(string $credentialId): void
    {
        $sql = 'CALL "p_ResetPassword"(:pwd, :credentialId)';
        $pwd = '';
        $args = [
            ':pwd' => $pwd,
            ':credentialId' => $credentialId
        ];
        $options = [
            ':pwd' => PDO::PARAM_STR
        ];
        $this->context->ExecuteQuery($sql, $args);
    }

    public function SetConnectionStatus(string $credentialId, bool $connected, ?string $sessionId = null): void
    {
        $sql = 'CALL "p_ConnectionStatus"(:credentialId, :connected, :sessionId)';
        $args = [
            ':credentialId' => $credentialId,
            ':connected' => $connected,
            ':sessionId' => $sessionId
        ];
        $options = [
            ':connected' => PDO::PARAM_BOOL
        ];
        $this->context->ExecuteQuery($sql, $args, $options);
    }

    public function CheckConnectionStatus(string $credentialId): bool
    {
        $sql = sprintf('SELECT * FROM "%s"(\'%s\')', 'f_CheckConnectionStatus', $credentialId);
        return (bool)$this->context->ExecuteQuery($sql)[0];
    }

    public function SetLoginStatus(string $credentialId, string $loginStatus, string $ip): void
    {
        $sql = 'CALL "p_LoginStatus"(:credentialId, :loginStatus, :ip)';
        $args = [
            ':credentialId' => $credentialId,
            ':loginStatus' => $loginStatus,
            ':ip' => $ip
        ];
        $this->context->ExecuteQuery($sql, $args);
    }

    public function CheckLoginStatus(string $credentialId): string
    {
        $sql = sprintf('SELECT * FROM "%s"(\'%s\')', 'f_CheckLoginStatus', $credentialId);
        return $this->context->ExecuteQuery($sql)[0];
    }

    public function SetCurrentThread(string $credentialId, int $threads): void
    {
        $sql = 'CALL "p_CurrentThread"(:threaded, :credentialId, :threads)';
        $threaded = false;
        $args = [
            ':threaded' => $threaded,
            ':credentialId' => $credentialId,
            ':threads' => $threads
        ];
        $options = [
            ':threaded' => PDO::PARAM_BOOL,
            ':threads' => PDO::PARAM_INT
        ];
        $this->context->ExecuteQuery($sql, $args, $options);
    }

    public function CheckCurrentThread(): bool
    {
        $sql = sprintf('SELECT * FROM "%s"()', 'f_CheckCurrentThread');
        return (bool)$this->context->ExecuteQuery($sql)[0];
    }

    /**
     * @throws DBException
     * @throws Exception
     */
    public function CheckCredential(string $username, string $password, string $ip): ?Credential
    {
        $sql = sprintf('SELECT * FROM "%s"(\'%s\', \'%s\', \'%s\')', 'f_CheckCredential', $username, $password, $ip);
        $data = $this->context->ExecuteQuery($sql);

        if (empty($data))
            return null;

        if (count($data) > 1)
            throw new DBException("Query returned more than one result");

        $entityName = $this->GetEntityName('CheckCredential');
        $entityName = strtolower(explode('\\', $entityName)[1]);
        return $this->context->Mapping($entityName, $data[0]);
    }
}