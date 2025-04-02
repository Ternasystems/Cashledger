<?php
/* Presentation Choose User page */

use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

// Read locales
$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\HomeLocale.xml');

// Instantiate Locales
$locales = new Locales();

// View data
$credentials = $ViewData["credentials"];

// Localizer
$Localizer = ['ChooseUser' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Home', 'ChooseUser')];
?>

    <div class="text-start pb-2"><?= $Localizer['ChooseUser'] ?></div>

<?php
foreach ($credentials as $credential) {
    $profile = $credential->Profile();
    $UserName = $credential->It()->UserName;
    $LastName = $profile->It()->LastName;
    ?>
    <a class="ts-usr" data-app="Presentation" data-parent="body" data-component="SelectUser" data-value="<?= $UserName ?>"><?= $LastName ?></a>
    <?php
}