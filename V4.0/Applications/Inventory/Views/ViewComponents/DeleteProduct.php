<?php
// View data
$components = $ViewData["components"];
$lang = $ViewData['CurrentLanguage'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\ModifyProductLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'ProductName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductName'),
    'ProductDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductDesc'),
    'ProductSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductSuccessBtn'),
    'ProductList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductList')
];
?>
<div id="main">
    <div id="product-delete-list">
        <div class="title">
            <span><?= $Localizer['ProductList']; ?></span>
        </div>
        <?php
        for ($i = 0; $i < 5; $i++){
            ?>
            <div class="product-elt ts-elt d-flex justify-content-between">
                <span><?= 'Product '.($i+1); ?></span>
                <span class="bi bi-trash"></span>
            </div>
            <?php
        }
        ?>
    </div>
</div>