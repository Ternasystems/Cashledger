<?php

namespace API_InventoryRepositories_Context;

use API_DTORepositories_Context\TContext;
use API_InventoryRepositories_Collection\Customers;
use API_InventoryRepositories_Collection\DeliveryNotes;
use API_InventoryRepositories_Collection\DispatchNotes;
use API_InventoryRepositories_Collection\InventNotes;
use API_InventoryRepositories_Collection\Inventories;
use API_InventoryRepositories_Collection\Manufacturers;
use API_InventoryRepositories_Collection\Packagings;
use API_InventoryRepositories_Collection\ProductAttributes;
use API_InventoryRepositories_Collection\ProductCategories;
use API_InventoryRepositories_Collection\Products;
use API_InventoryRepositories_Collection\ReturnNotes;
use API_InventoryRepositories_Collection\Stocks;
use API_InventoryRepositories_Collection\Suppliers;
use API_InventoryRepositories_Collection\Units;
use API_InventoryRepositories_Collection\Warehouses;
use API_InventoryRepositories_Collection\WasteNotes;
use API_InventoryRepositories_Model\Customer;
use API_InventoryRepositories_Model\DeliveryNote;
use API_InventoryRepositories_Model\DispatchNote;
use API_InventoryRepositories_Model\InventNote;
use API_InventoryRepositories_Model\Inventory;
use API_InventoryRepositories_Model\Manufacturer;
use API_InventoryRepositories_Model\Packaging;
use API_InventoryRepositories_Model\Product;
use API_InventoryRepositories_Model\ProductAttribute;
use API_InventoryRepositories_Model\ProductCategory;
use API_InventoryRepositories_Model\ReturnNote;
use API_InventoryRepositories_Model\Stock;
use API_InventoryRepositories_Model\Supplier;
use API_InventoryRepositories_Model\Unit;
use API_InventoryRepositories_Model\Warehouse;
use API_InventoryRepositories_Model\WasteNote;
use PDO;
use TS_Database\Classes\DBContext;

class InventoryContext extends DBContext
{
    protected PDO $pdo;
    private string $customer = 'cl_Customers';
    private string $inventory = 'cl_Inventories';
    private string $manufacturer = 'cl_Manufacturers';
    private string $packaging = 'cl_Packagings';
    private string $product = 'cl_Products';
    private string $productattribute = 'cl_ProductAttributes';
    private string $productcategory = 'cl_ProductCategories';
    private string $stock = 'cl_Stocks';
    private string $supplier = 'cl_Suppliers';
    private string $unit = 'cl_Units';
    private string $warehouse = 'cl_Warehouses';
    private string $deliverynote = 'cl_DeliveryNotes';
    private string $dispatchnote = 'cl_DispatchNotes';
    private string $returnnote = 'cl_ReturnNotes';
    private string $wastenote = 'cl_WasteNotes';
    private string $inventnote = 'cl_InventNotes';

    public function __construct(array $_connectionString){
        $this->pdo = DBContext::GetConnection($_connectionString);
        $this->SetEntityMap();
        $this->SetPropertyMap();
    }

    use TContext;

    private function SetEntityMap(): void
    {
        $this->entityMap = [
            'customer' => Customer::class,
            'inventory' => Inventory::class,
            'manufacturer' => Manufacturer::class,
            'packaging' => Packaging::class,
            'product' => Product::class,
            'productattribute' => ProductAttribute::class,
            'productcategory' => ProductCategory::class,
            'stock' => Stock::class,
            'supplier' => Supplier::class,
            'unit' => Unit::class,
            'warehouse' => Warehouse::class,
            'deliverynote' => DeliveryNote::class,
            'dispatchnote' => DispatchNote::class,
            'returnnote' => ReturnNote::class,
            'wastenote' => WasteNote::class,
            'inventnote' => InventNote::class,
            'customercollection' => Customers::class,
            'inventorycollection' => Inventories::class,
            'manufacturercollection' => Manufacturers::class,
            'packagingcollection' => Packagings::class,
            'productcollection' => Products::class,
            'productattributecollection' => ProductAttributes::class,
            'productcategorycollection' => ProductCategories::class,
            'stockcollection' => Stocks::class,
            'suppliercollection' => Suppliers::class,
            'unitcollection' => Units::class,
            'warehousecollection' => Warehouses::class,
            'deliverynotecollection' => DeliveryNotes::class,
            'dispatchnotecollection' => DispatchNotes::class,
            'returnnotecollection' => ReturnNotes::class,
            'wastenotecollection' => WasteNotes::class,
            'inventnotecollection' => InventNotes::class
        ];
    }

    private function SetPropertyMap(): void
    {
        $this->propertyMap = [
            'ID' => 'Id',
            'ProfileID' => 'ProfileId',
            'CategoryID' => 'CategoryId',
            'ProductID' => 'ProductId',
            'UnitID' => 'UnitId',
            'WarehouseID' => 'WarehouseId',
            'PackagingID' => 'PackagingId',
            'StockID' => 'StockId',
            'DeliveryID' => 'DeliveryId',
            'DispatchID' => 'DispatchId',
            'ReturnID' => 'ReturnId',
            'WasteID' => 'WasteId',
            'InventID' => 'InventId',
            'PartnerID' => 'PartnerId',
            'NoteID' => 'NoteId'
        ];
    }
}