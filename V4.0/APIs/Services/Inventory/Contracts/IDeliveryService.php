<?php

namespace API_Inventory_Contract;

use API_InventoryEntities_Collection\DeliveryNotes;
use API_InventoryEntities_Model\DeliveryNote;

interface IDeliveryService
{
    public function GetDeliveries(callable $predicate = null): DeliveryNote|DeliveryNotes|null;
    public function SetDelivery(object $model): void;
    public function PutDelivery(object $model): void;
    public function DeleteDelivery(string $id): void;
}