<?php
/* Inventory home template */

// Read locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

// Set statics
$ViewData['css'] = $ViewData['cssPath'].'css.Inventory/StockOut.css';
$ViewData['js'] = $ViewData['jsPath'].'js.Inventory/InventoryStockOut.js';

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\StockOutLocale.xml');
$menuLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\ContextMenuLocale.xml');

// Instantiate Locales
$locales = new Locales();

$title = $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'Title');

// Include Home menu View Component
$Localizer = [
    'MenuOverview' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'MenuOverview'),
    'GeneralOverview' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'GeneralOverview'),
    'WareHouseOverview' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'WareHouseOverview'),
    'ProductOverview' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'ProductOverview'),
    'CustomerOverview' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'CustomerOverview'),
    'SupplierOverview' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'SupplierOverview'),
    'MenuOperation' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'MenuOperation'),
    'StockIn' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'StockIn'),
    'StockOut' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'StockOut'),
    'Inventories' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Inventories'),
    'MenuConfig' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'MenuConfig'),
    'ProductCategories' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'ProductCategories'),
    'ProductAttributes' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'ProductAttributes'),
    'Products' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Products'),
    'Warehouses' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Warehouses'),
    'Customers' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Customers'),
    'Suppliers' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Suppliers'),
    'Manufacturers' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Manufacturers'),
    'Units' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Units'),
    'Packaging' => $locales->getLocale($menuLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Packaging')
];

$MenuItems = [
    'MenuOverview' => ['GeneralOverview', 'WareHouseOverview', 'ProductOverview', 'CustomerOverview', 'SupplierOverview'],
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