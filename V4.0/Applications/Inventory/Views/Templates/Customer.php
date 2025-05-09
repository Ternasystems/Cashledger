<?php
/* Product categories template */

// Read locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

// Set statics
$ViewData['css'] = $ViewData['cssPath'].'css.Inventory/Customer.css';
$ViewData['js'] = $ViewData['jsPath'].'js.Inventory/InventoryCustomer.js';

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\CustomerLocale.xml');

// Instantiate Locales
$locales = new Locales();

$title = $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'Title');

// Include Home menu View Component
$Localizer = [
    'MenuOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'MenuOverview'),
    'GeneralOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'GeneralOverview'),
    'WareHouseOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'WareHouseOverview'),
    'ProductOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'ProductOverview'),
    'StockOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'StockOverview'),
    'MenuOperation' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'MenuOperation'),
    'StockIn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'StockIn'),
    'StockOut' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'StockOut'),
    'Inventories' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'Inventories'),
    'MenuConfig' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'MenuConfig'),
    'ProductCategories' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'ProductCategories'),
    'ProductAttributes' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'ProductAttributes'),
    'Products' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'Products'),
    'Warehouses' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'Warehouses'),
    'Customers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'Customers'),
    'Suppliers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'Suppliers'),
    'Manufacturers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'Manufacturers'),
    'Units' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'Units'),
    'Packaging' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'Packaging')
];

$MenuItems = [
    'MenuOverview' => ['GeneralOverview', 'WareHouseOverview', 'ProductOverview', 'StockOverview'],
    'MenuOperation' => ['StockIn', 'StockOut', 'Inventories'],
    'MenuConfig' => ['ProductCategories', 'ProductAttributes', 'Products', 'Warehouses', 'Customers', 'Suppliers', 'Manufacturers', 'Units', 'Packaging'],
];

$NavLocales = [
    'NavTitle' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'NavTitle'),
    'NavNewCustomer' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'NavNewCustomer')
    /*'NavModifyCustomer' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'NavModifyCustomer'),
    'NavDeleteCustomer' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Customer', 'NavDeleteCustomer')*/
];

//$NavItems = ['NavNewCustomer', 'NavModifyCustomer', 'NavDeleteCustomer'];
$NavItems = ['NavNewCustomer'];

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