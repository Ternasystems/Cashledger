<?php

namespace API_ProfilingRepositories;

use API_DTORepositories_Model\DTOBase;
use API_Assets\Classes\DTOException;
use API_DTORepositories\Repository;
use API_ProfilingRepositories_Collection\Tokens;
use API_ProfilingRepositories_Context\TokenContext;
use API_ProfilingRepositories_Model\Token;

/**
 * @extends Repository<Token, Tokens>
 */
class TokenRepository extends Repository
{
    private ?string $roleName = null;

    public function __construct(TokenContext $context)
    {
        parent::__construct($context, Token::class, Tokens::class);
    }

    public function SetTableName(string $_roleName): void
    {
        $this->roleName = $_roleName;
    }

    public function GetTableName(): string
    {
        return $this->roleName;
    }

    /**
     * @throws DTOException
     */
    public function add(DTOBase $entity): void
    {
        if (!($entity instanceof Token)) {
            throw new DTOException('invalid_argument');
        }

        $args = get_object_vars($entity);
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

    /**
     * @throws DTOException
     */
    public function update(DTOBase $entity): void
    {
        if (!($entity instanceof Token)) {
            throw new DTOException('invalid_argument');
        }

        $args = get_object_vars($entity);
        $this->context->Update($this->roleName, $args);
    }
}