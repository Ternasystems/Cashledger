<?php

namespace API_Profiling_Service;

use API_Administration_Service\ReloadMode;
use API_Assets\Classes\EntityException;
use API_Assets\Classes\ProfilingException;
use API_Profiling_Contract\ICredentialService;
use API_ProfilingEntities_Collection\Credentials;
use API_ProfilingEntities_Factory\CredentialFactory;
use API_ProfilingEntities_Model\Credential;
use API_RelationRepositories\RoleRelationRepository;
use API_RelationRepositories_Model\RoleRelation;
use Throwable;
use TS_Exception\Classes\DomainException;

class CredentialService implements ICredentialService
{
    protected CredentialFactory $credentialFactory;
    protected Credentials $credentials;
    protected RoleRelationRepository $roleRelationRepository;

    public function __construct(CredentialFactory $credentialFactory, RoleRelationRepository $roleRelationRepository)
    {
        $this->credentialFactory = $credentialFactory;
        $this->roleRelationRepository = $roleRelationRepository;
    }

    /**
     * @inheritDoc
     * @throws DomainException
     */
    public function getCredentials(?array $filter = null, int $page = 1, int $pageSize = 10, ReloadMode $reloadMode = ReloadMode::NO): Credential|Credentials|null
    {
        if (!isset($this->credentials) || $reloadMode === ReloadMode::YES) {
            $offset = (is_null($page) || is_null($pageSize)) ? null : (($page - 1) * $pageSize);

            $this->credentialFactory->filter($filter, $pageSize, $offset);
            $this->credentialFactory->Create();
            $this->credentials = $this->credentialFactory->collectable();
        }

        if (count($this->credentials) === 0)
            return null;

        return $this->credentials->count() > 1 ? $this->credentials : $this->credentials->first();
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function SetCredential(array $data): Credential
    {
        $context = $this->credentialFactory->repository()->context;
        $context->beginTransaction();

        try {
            // 1. Create and save the main credential DTO
            $credential = new \API_ProfilingRepositories_Model\Credential([
                'UserName' => $data['UserName'],
                'UserPassword' => hash('sha256', $data['UserPassword']), // Always hash passwords
                'ProfileId' => $data['ProfileId']
            ]);
            $this->credentialFactory->repository()->add($credential);

            // 2. Get the newly created credential
            $credential = $this->credentialFactory->repository()->first([['UserName', '=', $data['UserName']]]);
            if (!$credential)
                throw new ProfilingException('credential_creation_failed');

            // 3. Create and save the role relation
            if (isset($data['RoleId'])) {
                $relation = new RoleRelation([
                    'CredentialId' => $credential->Id,
                    'RoleId' => $data['RoleId']
                ]);
                $this->roleRelationRepository->add($relation);
            }

            $context->commit();

            // 4. Fetch the complete, rich entity to return
            return $this->getCredentials([['Id', '=', $credential->Id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e) {
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function PutCredential(string $id, array $data): ?Credential
    {
        $context = $this->credentialFactory->repository()->context;
        $context->beginTransaction();

        try {
            $credential = $this->getCredentials([['Id', '=', $id]])?->first();
            if (!$credential) {
                throw new ProfilingException('credential_not_found', ["Id" => $id]);
            }

            // 1. Update the main credential record
            $credential->it()->UserName = $data['UserName'] ?? $credential->it()->UserName;
            $credential->it()->ProfileId = $data['ProfileId'] ?? $credential->it()->ProfileId;
            $this->credentialFactory->repository()->update($credential->it());

            // 2. Delete old role relation
            if ($credential->Role()) {
                $relation = $this->roleRelationRepository->first([['CredentialId', '=', $credential->it()->Id]]);
                $this->roleRelationRepository->remove($relation->Id);
            }

            // 3. Add new role relation
            if (isset($data['RoleId'])) {
                $relation = new RoleRelation([
                    'CredentialId' => $credential->it()->Id,
                    'RoleId' => $data['RoleId']
                ]);
                $this->roleRelationRepository->add($relation);
            }

            $context->commit();

            return $this->getCredentials([['Id', '=', $id]], 1, 1, ReloadMode::YES);

        } catch (Throwable $e) {
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @inheritDoc
     * @throws Throwable
     */
    public function DeleteCredential(string $id): bool
    {
        $context = $this->credentialFactory->repository()->context;
        $context->beginTransaction();

        try {
            $credential = $this->getCredentials([['Id', '=', $id]])?->first();
            if (!$credential) {
                $context->commit();
                return true;
            }

            // Deactivate role relation
            if ($credential->Role()) {
                $relation = $this->roleRelationRepository->first([['CredentialId', '=', $id]]);
                $this->roleRelationRepository->deactivate($relation->Id);
            }

            // Deactivate the main credential
            $this->credentialFactory->repository()->deactivate($id);

            $context->commit();
            return true;
        } catch (Throwable $e) {
            $context->rollBack();
            throw $e;
        }
    }

    /**
     * @throws DomainException
     * @throws ProfilingException
     * @throws EntityException
     */
    public function PutPassword(string $id, ?string $password = null): bool
    {
        $credential = $this->getCredentials([['Id', '=', $id]])?->first();
        if (!$credential)
            throw new ProfilingException('credential_not_found', ["Id" => $id]);

        if (is_null($password))
            $this->credentialFactory->repository()->resetPassword($id);
        else {
            $oldPwd = $credential->UserPassword;
            $this->credentialFactory->repository()->updatePassword($id, $oldPwd, $password);
        }

        return true;
    }
}