<?php

namespace API_ProfilingRepositories;

use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Credentials;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Model\Credential;
use Closure;
use Exception;
use PDO;

class CredentialRepository extends Repository
{
    public function __construct(ProfilingContext $context)
    {
        parent::__construct($context);
    }

    public function FirstOrDefault(?callable $predicate = null): ?Credential
    {
        $entity = parent::first($predicate);
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

    public function GetBy(Closure $predicate): ?Credentials
    {
        $collection = parent::GetBy($predicate);
        return $collection instanceof Credentials ? $collection : null;
    }

    public function LastOrDefault(?callable $predicate = null): ?Credential
    {
        $entity = parent::last($predicate);
        return $entity instanceof Credential ? $entity : null;
    }

    // --- Custom Repository Methods ---

    public function updatePassword(string $credentialId, string $oldPassword, string $newPassword): void
    {
        // Use the new public ExecuteCommand method from the context.
        $this->context->ExecuteCommand(
            'CALL "p_UpdatePassword"(?, ?, ?)',
            [$credentialId, $oldPassword, $newPassword]
        );
    }

    public function resetPassword(string $credentialId): string
    {
        // For procedures with OUT parameters, use the new Prepare method.
        $stmt = $this->context->Prepare('CALL "p_ResetPassword"(?, ?)');
        $stmt->bindParam(1, $pwd, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 50);
        $stmt->bindParam(2, $credentialId, PDO::PARAM_STR);
        $stmt->execute();
        return $pwd;
    }

    public function setConnectionStatus(string $credentialId, bool $connected, ?string $sessionId = null): void
    {
        $this->context->ExecuteCommand(
            'CALL "p_ConnectionStatus"(?, ?, ?)',
            [$credentialId, $connected, $sessionId]
        );
    }

    public function checkConnectionStatus(string $credentialId): bool
    {
        // For functions returning a single row, use the new ExecuteSelectOne method.
        $result = $this->context->ExecuteSelectOne(
            'SELECT "f_CheckConnectionStatus"(?) AS status',
            [$credentialId]
        );
        return $result && $result['status'];
    }

    public function setLoginStatus(string $credentialId, string $loginStatus, string $ip): void
    {
        $this->context->ExecuteCommand(
            'CALL "p_LoginStatus"(?, ?, ?)',
            [$credentialId, $loginStatus, $ip]
        );
    }

    public function checkLoginStatus(string $credentialId): ?string
    {
        $result = $this->context->ExecuteSelectOne(
            'SELECT "f_CheckLoginStatus"(?) AS status',
            [$credentialId]
        );
        return $result ? $result['status'] : null;
    }

    public function setCurrentThread(string $credentialId, int $threads): bool
    {
        $stmt = $this->context->Prepare('CALL "p_SetCurrentThread"(?, ?, ?)');
        $stmt->bindParam(1, $isThreaded, PDO::PARAM_BOOL | PDO::PARAM_INPUT_OUTPUT);
        $stmt->bindParam(2, $credentialId, PDO::PARAM_STR);
        $stmt->bindParam(3, $threads, PDO::PARAM_INT);
        $stmt->execute();
        return $isThreaded;
    }

    public function checkCurrentThread(): bool
    {
        $result = $this->context->ExecuteSelectOne('SELECT "f_CheckCurrentThread"() AS status');
        return $result && $result['status'];
    }

    /**
     * @throws Exception
     */
    public function checkCredential(string $username, string $password, string $ip): ?Credential
    {
        $data = $this->context->ExecuteSelectOne(
            'SELECT * FROM "f_CheckCredential"(?, ?, ?)',
            [$username, $password, $ip]
        );

        if (empty($data)) {
            return null;
        }

        $entity = $this->context->Mapping($this->entityName, $data);
        return $entity instanceof Credential ? $entity : null;
    }
}