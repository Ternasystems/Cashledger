<?php

namespace API_InventoryRepositories;

use API_DTORepositories\Repository;
use API_InventoryRepositories_Collection\Stocks;
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories_Model\Stock;

/**
 * @extends Repository<Stock, Stocks>
 */
class StockRepository extends Repository
{
    public function __construct(InventoryContext $context)
    {
        parent::__construct($context, Stock::class, Stocks::class);
    }

    // --- Custom Repository Methods ---

    public function updateQuantity(string $id, float $quantity): void
    {
        // Use the new public ExecuteCommand method from the context.
        $this->context->ExecuteCommand(
            'CALL "p_UpdateQuantity"(?, ?)',
            [$id, $quantity]
        );
    }
}