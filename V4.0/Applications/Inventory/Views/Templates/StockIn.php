<?php
/* Inventory home template */

// Read locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

// Set statics
$ViewData['css'] = $ViewData['cssPath'].'css.Inventory/StockIn.css';
$ViewData['js'] = $ViewData['jsPath'].'js.Inventory/InventoryStockIn.js';

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\StockInLocale.xml');

// Instantiate Locales
$locales = new Locales();

$title = $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'Title');

// Include Home menu View Component
$Localizer = [
    'MenuOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'MenuOverview'),
    'GeneralOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'GeneralOverview'),
    'WareHouseOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'WareHouseOverview'),
    'ProductOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'ProductOverview'),
    'StockOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'StockOverview'),
    'MenuOperation' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'MenuOperation'),
    'StockIn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'StockIn'),
    'StockOut' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'StockOut'),
    'Inventories' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'Inventories'),
    'MenuConfig' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'MenuConfig'),
    'ProductCategories' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'ProductCategories'),
    'ProductAttributes' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'ProductAttributes'),
    'Products' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'Products'),
    'Warehouses' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'Warehouses'),
    'Customers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'Customers'),
    'Suppliers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'Suppliers'),
    'Manufacturers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'Manufacturers'),
    'Units' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'Units'),
    'Packaging' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'Packaging')
];

$MenuItems = [
    'MenuOverview' => ['GeneralOverview', 'WareHouseOverview', 'ProductOverview', 'StockOverview'],
    'MenuOperation' => ['StockIn', 'StockOut', 'Inventories'],
    'MenuConfig' => ['ProductCategories', 'ProductAttributes', 'Products', 'Warehouses', 'Customers', 'Suppliers', 'Manufacturers', 'Units', 'Packaging'],
];

$NavLocales = [
    'NavTitle' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'NavTitle'),
    'NavDeliveryList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'NavDeliveryList'),
    'NavStockDelivery' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'NavStockDelivery'),
    'NavStockReturn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'NavStockReturn')
];

$NavItems = ['NavDeliveryList', 'NavStockDelivery', 'NavStockReturn'];

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