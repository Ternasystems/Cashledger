<?php

namespace API_Inventory_Contract;

use API_InventoryEntities_Collection\DispatchNotes;
use API_InventoryEntities_Model\DispatchNote;

interface IDispatchService
{
    public function GetDispatches(callable $predicate = null): DispatchNote|DispatchNotes|null;
    public function SetDispatch(object $model): void;
    public function PutDispatch(object $model): void;
    public function DeleteDispatch(string $id): void;
}