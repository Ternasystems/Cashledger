<?php
// View data
$components = $ViewData["components"];
$lang = $ViewData['CurrentLanguage'];
$products = $ViewData["products"];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewProductLocale.xml');

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
    <form id="new-product" name="new-product" method="post" action="NewProduct">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavNewProduct'][$lang]['title']; ?></span>
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
    <div id="product-list">
        <div class="title">
            <span><?= $Localizer['ProductList']; ?></span>
        </div>
        <?php
        if (isset($products)) {
            foreach ($products->SortBy(fn($n) => $n->It()->Name) as $product) {
                ?>
                <div class="category-elt"><?= $product->It()->Name; ?></div>
                <?php
            }
        }
        ?>
    </div>
</div>