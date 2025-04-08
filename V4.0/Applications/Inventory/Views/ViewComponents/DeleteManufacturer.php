<?php
// View data
$components = $ViewData["components"];
$lang = $ViewData['CurrentLanguage'];
$manufacturers = $ViewData['manufacturers'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\ModifyManufacturerLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'ManufacturerName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Manufacturer', 'ManufacturerName'),
    'ManufacturerDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Manufacturer', 'ManufacturerDesc'),
    'ManufacturerSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Manufacturer', 'ManufacturerSuccessBtn'),
    'ManufacturerList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Manufacturer', 'ManufacturerList')
];
?>
<div id="main">
    <div id="manufacturer-delete-list">
        <div class="title">
            <span><?= $Localizer['ManufacturerList']; ?></span>
        </div>
        <?php
        if (isset($manufacturers)) {
            foreach ($manufacturers->SortBy(fn($n) => $n->It()->Name) as $manufacturer) {
                ?>
                <div class="manufacturer-elt ts-elt d-flex justify-content-between" data-id="<?= $manufacturer->It()->Id; ?>">
                    <span><?= $manufacturer->It()->Name; ?></span>
                    <span class="bi bi-trash"></span>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>