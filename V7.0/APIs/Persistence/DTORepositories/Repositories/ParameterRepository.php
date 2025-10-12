<?php

namespace API_DTORepositories;

use API_Assets\Classes\DTOException;
use API_DTORepositories_Collection\Parameters;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories_Model\Parameter;
use API_DTORepositories_Model\DTOBase;
use DateTime;

/**
 * @extends Repository<Parameter, Parameters>
 */
class ParameterRepository extends Repository
{
    public function __construct(DTOContext $context)
    {
        parent::__construct($context, Parameter::class, Parameters::class);
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        throw new DTOException('unauthorized_method');
    }

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        throw new DTOException('unauthorized_method');
    }

    // --- Custom Repository Methods ---

    public function getParameter(string $paramName): ?Parameter
    {
        $data = $this->context->ExecuteSelectOne('SELECT "f_ReadParameter"(?) AS Parameter', [$paramName]);

        if (empty($data)) {
            return null;
        }

        $entity = $this->context->Mapping($this->entityName, $data);
        return $entity instanceof Parameter ? $entity : null;
    }

    public function updateParameter(string $paramName, string $paramValue, bool $encrypted): void
    {
        // Use the new public ExecuteCommand method from the context.
        $this->context->ExecuteCommand(
            'CALL "p_UpdateParameter"(?, ?, ?)',
            [$paramName, $paramValue, $encrypted]
        );
    }

    public function getParameterFrom(string $predicate, ?array $args = null): string|float|null
    {
        $str = is_null($args) ? null : implode(', ', array_fill(0, count($args), '?'));
        $sql = sprintf('SELECT * FROM "%s"(%s)', $predicate, $str);

         return $this->context->ExecuteSelectOne($sql, $args ?? [])[$predicate];
    }

    public function checkParameter(string $predicate, ?array $args = null): bool
    {
        $str = is_null($args) ? null : implode(', ', array_fill(0, count($args), '?'));
        $sql = sprintf('SELECT * FROM "%s"(%s)', $predicate, $str);

        return $this->context->ExecuteSelectOne($sql, $args ?? [])[$predicate];
    }

    protected function getMachineId(): ?string
    {
        $command = 'reg query "HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\SQMClient" /v MachineId';
        $output = shell_exec($command);

        // Parse the registry output
        if (preg_match('/MachineId\s+REG_SZ\s+([a-fA-F0-9-]+)/', $output, $matches))
            return $matches[1];

        return null;
    }

    protected function activated(bool $activated): void
    {
        $this->context->ExecuteCommand(
            'CALL "p_Activation"(?)',
            [$activated]
        );
    }

    public function activation(string $activationCode): void
    {
        // Check activation code
        if (!preg_match('/^([A-Z0-9]+-){3}[A-Z0-9]+$/', $activationCode, $matches))
            return;

        // Get serial
        $serial = $this->getMachineId();

        // Get activation elements
        $startDate = $matches[0];
        $activeDate = $matches[1];
        $endDate = $matches[2];
        $users = $matches[3];

        // Update parameters
        $this->updateParameter('Serial', $serial, true);
        $this->updateParameter('startDate', $startDate, false);
        $this->updateParameter('ActiveDate', $activeDate, false);
        $this->updateParameter('endDate', $endDate, false);
        $this->updateParameter('Users', $users, false);
        $this->activated(true);
    }

    public function checkActivation(): bool
    {
        $result = $this->context->ExecuteSelectOne('SELECT "f_CheckActivation"(?, ?) AS Activated', [$this->getMachineId(), new DateTime()]);
        return $result && $result['Activated'];
    }

    public function checkPeriod(): bool
    {
        $result = $this->context->ExecuteSelectOne('SELECT "f_CheckPeriod"(?) AS Activated', [new DateTime()]);
        return $result && $result['Activated'];
    }
}