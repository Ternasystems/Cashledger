<?php
/* Inventory home template */

// Read locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

// Set statics
$ViewData['css'] = $ViewData['cssPath'].'css.Inventory/StockOut.css';
$ViewData['js'] = $ViewData['jsPath'].'js.Inventory/InventoryStockOut.js';

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\StockOutLocale.xml');

// Instantiate Locales
$locales = new Locales();

$title = $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'Title');

// Include Home menu View Component
$Localizer = [
    'MenuOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'MenuOverview'),
    'GeneralOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'GeneralOverview'),
    'WareHouseOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'WareHouseOverview'),
    'ProductOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'ProductOverview'),
    'StockOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'StockOverview'),
    'MenuOperation' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'MenuOperation'),
    'StockIn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'StockIn'),
    'StockOut' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'StockOut'),
    'Inventories' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'Inventories'),
    'MenuConfig' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'MenuConfig'),
    'ProductCategories' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'ProductCategories'),
    'ProductAttributes' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'ProductAttributes'),
    'Products' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'Products'),
    'Warehouses' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'Warehouses'),
    'Customers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'Customers'),
    'Suppliers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'Suppliers'),
    'Manufacturers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'Manufacturers'),
    'Units' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'Units'),
    'Packaging' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'Packaging')
];

$MenuItems = [
    'MenuOverview' => ['GeneralOverview', 'WareHouseOverview', 'ProductOverview', 'StockOverview'],
    'MenuOperation' => ['StockIn', 'StockOut', 'Inventories'],
    'MenuConfig' => ['ProductCategories', 'ProductAttributes', 'Products', 'Warehouses', 'Customers', 'Suppliers', 'Manufacturers', 'Units', 'Packaging'],
];

$NavLocales = [
    'NavTitle' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'NavTitle'),
    'NavDispatchList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'NavDispatchList'),
    'NavStockDispatch' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'NavStockDispatch'),
    'NavStockDisposal' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'NavStockDisposal')
];

$NavItems = ['NavDispatchList', 'NavStockDispatch', 'NavStockDisposal'];

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