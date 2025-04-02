<?php
/* Booking home template */

// Read locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

// Set statics
$ViewData['css'] = $ViewData['cssPath'].'css.Hrm/Index.css';
$ViewData['js'] = $ViewData['jsPath'].'js.Hrm/Hrm.js';

// Start buffering
ob_start();

// Insert Header View Component
$header = null;
require $ViewData['vcPath'].'Header.php';

// Insert Footer View Component
require $ViewData['vcPath'].'Footer.php';

$content = ob_get_clean();

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\HomeLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [ 'Title' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Hrm', 'Home', 'Title') ];

// Include layout
require $ViewData['layoutPath'].'_Layout.php';