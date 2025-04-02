<?php
/* Presentation Dashboard template */

// Read locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

// Set statics
$ViewData['css'] = $ViewData['cssPath'].'css.Presentation/Dashboard.css';
$ViewData['js'] = $ViewData['jsPath'].'js.Presentation/Dashboard.js';

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\DashboardLocale.xml');

// Instantiate Locales
$locales = new Locales();

$apptitle = $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Dashboard', 'Title');

$SidebarLocales = ['NavTitle' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Dashboard', 'NavTitle')];

$DashboardLocales = ['Launch' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Dashboard', 'Launch')];

// Start buffering
ob_start();

// Insert Header View Component
$header = null;
require $ViewData['vcPath'].'Header.php';

// Insert sidebar view component
$Localizer = $SidebarLocales;
include dirname(__DIR__, 1).'/ViewComponents/Sidebar.php';

// Insert apps dashboard view component
$Localizer = $DashboardLocales;
include dirname(__DIR__, 1).'/ViewComponents/AppDashboard.php';

// Insert Footer View Component
require $ViewData['vcPath'].'Footer.php';

$content = ob_get_clean();

$Localizer = ['Title' => $apptitle];

// Include layout
require $ViewData['layoutPath'].'_Layout.php';