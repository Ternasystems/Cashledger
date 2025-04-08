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
    <form id="modify-manufacturer" name="modify-manufacturer" method="post" action="UpdateManufacturer">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavModifyManufacturer'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Manufacturer name -->
            <div class="form-elt">
                <label for="manufacturername" class="me-2 text-end"><?= $Localizer['ManufacturerName']; ?></label>
                <input id="manufacturername" type="text" name="manufacturername" class="ts-form-control-light me-2"/>
                <input id="manufacturerid" type="hidden" name="manufacturerid" value="">
            </div>
            <!-- Manufacturer description -->
            <div class="form-elt">
                <label for="manufacturerdesc" class="me-2 text-end"><?= $Localizer['ManufacturerDesc']; ?></label>
                <input id="manufacturerdesc" type="text" name="manufacturerdesc" class="ts-form-control-light me-2"/>
            </div>
            <!-- Manufacturer name -->
            <div class="form-elt">
                <button class="btn btn-success"><?= $Localizer['ManufacturerSuccessBtn'] ?></button>
            </div>
        </div>
    </form>
    <!-- -->
    <div id="manufacturer-list">
        <div class="title">
            <span><?= $Localizer['ManufacturerList']; ?></span>
        </div>
        <?php
        if (isset($manufacturers)) {
            foreach ($manufacturers as $manufacturer) {
                ?>
                <div class="manufacturer-elt ts-elt" data-form="#modify-manufacturer" data-id="<?= $manufacturer->It()->Id; ?>"><?= $manufacturer->It()->Name; ?></div>
                <?php
            }
        }
        ?>
    </div>
</div>