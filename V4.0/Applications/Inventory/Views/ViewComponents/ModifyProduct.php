<?php
// View data
$components = $ViewData["components"];
$lang = $ViewData['CurrentLanguage'];
$products = $ViewData['products'];
$languages = $ViewData['languages'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\ModifyProductLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'ProductName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductName'),
    'ProductLabel' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductLabel'),
    'ProductDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductDesc'),
    'ProductSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductSuccessBtn'),
    'ProductList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductList')
];
?>
<div id="main">
    <form id="modify-product" name="modify-product" method="post" action="UpdateProduct">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavModifyProduct'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Product name -->
            <div class="form-elt">
                <label for="productname" class="me-2 text-end"><?= $Localizer['ProductName']; ?></label>
                <input id="productname" type="text" name="productname" class="ts-form-control-light me-2"/>
                <input id="productid" type="hidden" name="productid" value="">
            </div>
            <!-- Product label -->
            <div class="form-elt">
                <label for="productlabel" class="me-2 text-end"><?= $Localizer['ProductLabel']; ?></label>
                <input id="productlabel" type="text" name="productlabel" class="ts-form-control-light me-2"/>
            </div>
            <!-- Product description -->
            <div class="form-elt">
                <label for="productdesc" class="me-2 text-end"><?= $Localizer['ProductDesc']; ?></label>
                <input id="productdesc" type="text" name="productdesc" class="ts-form-control-light me-2"/>
            </div>
            <!-- Product name -->
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
            $langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
            $relations = null;
            foreach ($products as $product) {
                if ($product->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId) == null) continue;
                $relations[$product->It()->Id] = $product->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
            }
            asort($relations);
            foreach ($relations as $key => $relation) {
                ?>
                <div class="product-elt ts-elt" data-form="#modify-product" data-id="<?= $key; ?>"><?= $relation ?></div>
                <?php
            }
        }
        ?>
    </div>
</div>