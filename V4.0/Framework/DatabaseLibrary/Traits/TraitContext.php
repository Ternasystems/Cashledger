<?php
/*
 * This website is powered by Ternary Data Systems.
 * Ternary Data Systems is the technology department of the Jéoline company
 * This website is based on the Hypergates framework.
 */

declare(strict_types=1);

namespace TS_Database\Traits;

use BadMethodCallException;

trait TraitContext
{
    public function SelectAll(string $entityName): array{
        throw new BadMethodCallException("Add method must be implemented in subclass");
    }

    public function SelectById(string $Id, string $entityName): array{
        throw new BadMethodCallException("Add method must be implemented in subclass");
    }

    public function Insert(string $entityName, ?array $args = null) : void{
        throw new BadMethodCallException("Add method must be implemented in subclass");
    }

    public function Update(string $entityName, ?array $args = null) : void{
        throw new BadMethodCallException("Add method must be implemented in subclass");
    }

    public function Delete(string $entityName, ?array $args = null) : void{
        throw new BadMethodCallException("Add method must be implemented in subclass");
    }

    public function Disable(string $entityName, ?array $args = null) : void{
        throw new BadMethodCallException("Add method must be implemented in subclass");
    }

    public function Query(string $statement, ?array $args = null): array{
        $this->GetProcQuery()->SetStatement($statement);
        return $this->GetProcQuery()->QueryPDO($this->pdo, $args, 1);
    }

    public function ExecuteQuery(string $statement, ?array $args = null, ?array $options = null): array{
        $this->GetExecQuery()->SetStatement($statement);
        return $this->GetExecQuery()->QueryPDO($this->pdo, $args, 1, $options);
    }
}