<?php
/* Product categories template */

// Read locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

// Set statics
$ViewData['css'] = $ViewData['cssPath'].'css.Inventory/Supplier.css';
$ViewData['js'] = $ViewData['jsPath'].'js.Inventory/InventorySupplier.js';

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\SupplierLocale.xml');

// Instantiate Locales
$locales = new Locales();

$title = $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Title');

// Include Home menu View Component
$Localizer = [
    'MenuOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'MenuOverview'),
    'GeneralOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'GeneralOverview'),
    'WareHouseOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'WareHouseOverview'),
    'ProductOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'ProductOverview'),
    'StockOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'StockOverview'),
    'MenuOperation' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'MenuOperation'),
    'StockIn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'StockIn'),
    'StockOut' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'StockOut'),
    'Inventories' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Inventories'),
    'MenuConfig' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'MenuConfig'),
    'ProductCategories' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'ProductCategories'),
    'ProductAttributes' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'ProductAttributes'),
    'Products' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Products'),
    'Warehouses' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Warehouses'),
    'Customers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Customers'),
    'Suppliers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Suppliers'),
    'Manufacturers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Manufacturers'),
    'Units' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Units'),
    'Packaging' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Packaging')
];

$MenuItems = [
    'MenuOverview' => ['GeneralOverview', 'WareHouseOverview', 'ProductOverview', 'StockOverview'],
    'MenuOperation' => ['StockIn', 'StockOut', 'Inventories'],
    'MenuConfig' => ['ProductCategories', 'ProductAttributes', 'Products', 'Warehouses', 'Customers', 'Suppliers', 'Manufacturers', 'Units', 'Packaging'],
];

$NavLocales = [
    'NavTitle' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'NavTitle'),
    'NavNewSupplier' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'NavNewSupplier')
    /*'NavModifySupplier' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'NavModifySupplier'),
    'NavDeleteSupplier' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'NavDeleteSupplier')*/
];

//$NavItems = ['NavNewSupplier', 'NavModifySupplier', 'NavDeleteSupplier'];
$NavItems = ['NavNewSupplier'];

// Start buffering
ob_start();

// Insert Header View Component
include dirname(__DIR__, 1).'/ViewComponents/HomeMenu.php';
$header = ob_get_clean();

ob_start();

require $ViewData['vcPath'].'Header.php';

$Localizer = $NavLocales;
require $ViewData['vcPath'].'Navbar.php';

include dirname(__DIR__, 1).'/ViewComponents/Content.php';

// Insert Footer View Component
require $ViewData['vcPath'].'Footer.php';

$content = ob_get_clean();

$Localizer = [ 'Title' => $title ];

// Include layout
require $ViewData['layoutPath'].'_Layout.php';