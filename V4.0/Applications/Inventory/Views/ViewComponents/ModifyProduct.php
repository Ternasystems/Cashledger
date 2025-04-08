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
    <form id="modify-product" name="modify-product" method="post" action="ModifyProduct">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavModifyProduct'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Category name -->
            <div class="form-elt">
                <label for="productname" class="me-2 text-end"><?= $Localizer['ProductName']; ?></label>
                <input id="productname" type="text" name="productname" class="ts-form-control-light me-2"/>
            </div>
            <!-- Category description -->
            <div class="form-elt">
                <label for="productdesc" class="me-2 text-end"><?= $Localizer['ProductDesc']; ?></label>
                <input id="productdesc" type="text" name="productdesc" class="ts-form-control-light me-2"/>
            </div>
            <!-- Category name -->
            <div class="form-elt">
                <button class="btn btn-success"><?= $Localizer['ProductSuccessBtn'] ?></button>
            </div>
        </div>
    </form>
    <!-- -->
    <div id="category-list">
        <div class="title">
            <span><?= $Localizer['ProductList']; ?></span>
        </div>
        <?php
        for ($i = 0; $i < 5; $i++){
            ?>
            <div class="product-elt ts-elt"><?= 'Product '.($i+1); ?></div>
            <?php
        }
        ?>
    </div>
</div>