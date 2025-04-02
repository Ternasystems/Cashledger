<?php
/* Footer page */

// Read locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 4).'\Data\Locales\FooterLocale.xml');

// Instantiate Locales
$locales = new Locales();

// Languages
$languages = $ViewData['languages'];
$ft = $ViewData['ft'];

// Localizer
$Localizer = [
    'CompanyName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Dashboard', 'CompanyName'),
    'Fiscal' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Dashboard', 'Fiscal'),
    'Powered' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Dashboard', 'Powered')
];
?>

<footer>
    <div class="container-fluid d-flex justify-content-between align-items-center py-3">
        <div id="ft-1" class="d-flex">
            <select id="lang" data-app="<?= $ViewData['ft']['app'] ?>" data-controller="<?= $ViewData['ft']['ctrl'] ?>" data-action="<?= $ViewData['ft']['action'] ?>" class="ts-form-control-light">
                <?php
                foreach ($languages as $key => $language) {
                    ?>
                    <option value="<?= $language->LanguageRelations()[0]->Label; ?>" data-default="<?= $ViewData['CurrentLanguage'] ?>"><?= ucfirst($language->It()->Description) ?></option>
                    <?php
                }
                ?>
            </select>
            <div>
                <span><?= $ViewData['CompanyName']; ?></span>
                <span>(<?= $Localizer['Fiscal']; ?></span>
                <span>2025)</span>
            </div>
        </div>
        <div id="ft-2">
            <div>
                <span><?= $Localizer['Powered']; ?></span>
                <picture>
                    <source media="(min-width: 1024px)" srcset="<?= $ViewData['Jeoline_white']; ?>">
                    <img src="<?= $ViewData['Jeoline_white']; ?>">
                </picture>
            </div>
        </div>
    </div>
</footer>
