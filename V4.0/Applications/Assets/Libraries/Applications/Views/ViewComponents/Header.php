<?php
/* Header page */

// Read locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 4).'\Data\Locales\HeaderLocale.xml');

// Instantiate Locales
$locales = new Locales();

// Localizer
$Localizer = [
    'LogOut' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Dashboard', 'LogOut'),
    'MyProfile' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Dashboard', 'MyProfile'),
    'Help' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Dashboard', 'Help'),
    'Support' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Dashboard', 'Support'),
    'Copyright' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Dashboard', 'Copyright')
];
?>

<header>
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div id="hd-1" class="d-flex align-items-center">
            <span id="app-menu" class="bi bi-list fs-3 ts-link px-2" data-app="Presentation" data-controller="Dashboard" data-action="Index"></span>
            <?= $header; ?>
        </div>
        <div id="hd-2" class="ts-menu p-3">
            <span><?= $_SESSION['LastName']; ?></span>
            <span class="bi bi-three-dots-vertical"></span>
            <!-- Menu -->
            <ul class="ts-list d-none">
                <li class="ts-link" data-app="Presentation" data-controller="Home" data-action="Disconnect"><?= $Localizer['Help'] ?></li>
                <li class="ts-link" data-app="Presentation" data-controller="Home" data-action="Disconnect"><?= $Localizer['Support'] ?></li>
                <li class="ts-link border-bottom" data-app="Presentation" data-controller="Home" data-action="Disconnect"><?= $Localizer['Copyright'] ?></li>
                <li class="ts-link" data-app="Presentation" data-controller="Home" data-action="Disconnect"><?= $Localizer['MyProfile'] ?></li>
                <li class="ts-link text-danger" data-app="Presentation" data-controller="Home" data-action="Disconnect"><?= $Localizer['LogOut'] ?></li>
            </ul>
        </div>
    </div>
</header>