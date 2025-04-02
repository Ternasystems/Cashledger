<?php
/* Inventory home template */

// Read locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

// Set statics
$ViewData['css'] = $ViewData['cssPath'].'css.Inventory/Index.css';
$ViewData['js'] = $ViewData['jsPath'].'js.Inventory/Inventory.js';

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\HomeLocale.xml');

// Instantiate Locales
$locales = new Locales();

$title = $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Title');

// Include Home menu View Component
$Localizer = [
    'MenuOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'MenuOverview'),
    'GeneralOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'GeneralOverview'),
    'WareHouseOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'WareHouseOverview'),
    'ProductOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'ProductOverview'),
    'StockOverview' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'StockOverview'),
    'MenuOperation' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'MenuOperation'),
    'StockIn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'StockIn'),
    'StockOut' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'StockOut'),
    'Inventories' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Inventories'),
    'MenuConfig' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'MenuConfig'),
    'ProductCategories' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'ProductCategories'),
    'Products' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Products'),
    'Warehouses' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Warehouses'),
    'Customers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Customers'),
    'Suppliers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Suppliers'),
    'Manufacturers' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Manufacturers'),
    'Units' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Units'),
    'Packaging' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'Packaging')
];

$MenuItems = [
    'MenuOverview' => ['GeneralOverview', 'WareHouseOverview', 'ProductOverview', 'StockOverview'],
    'MenuOperation' => ['StockIn', 'StockOut', 'Inventories'],
    'MenuConfig' => ['ProductCategories', 'Products', 'Warehouses', 'Customers', 'Suppliers', 'Manufacturers', 'Units', 'Packaging'],
];

$NavLocales = [
    'NavTitle' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'NavTitle'),
    'NavGeneral' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'NavGeneral'),
    'NavWareHouse' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'NavWareHouse'),
    'NavProduct' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'NavProduct'),
    'NavStock' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Home', 'NavStock')
];

$NavItems = ['NavGeneral', 'NavWareHouse', 'NavProduct', 'NavStock'];

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