<?php
// View data
$components = $ViewData['components'];
$lang = $ViewData['CurrentLanguage'];
$products = $ViewData['products'];
$categories = $ViewData['categories'];
$units = $ViewData['units'];
$attributes = $ViewData['attributes'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\ModifyProductLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'ProductName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductName'),
    'CategoryId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'CategoryId'),
    'UnitId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'UnitId'),
    'MinStock' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'MinStock'),
    'MaxStock' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'MaxStock'),
    'ProductAttributes' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductAttributes'),
    'ProductLocaleFr' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductLocaleFr'),
    'ProductLocaleUs' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductLocaleUs'),
    'ProductDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductDesc'),
    'ProductSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductSuccessBtn'),
    'ProductList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'ProductList'),
    'CategorySelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'CategorySelect'),
    'UnitSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'UnitSelect'),
    'AttributeCheck' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'AttributeCheck'),
    'AttributeSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Product', 'AttributeSelect')
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
            <!-- Category ID -->
            <div class="form-elt">
                <label for="categoryid" class="me-2 text-end"><?= $Localizer['CategoryId']; ?></label>
                <select id="categoryid" name="categoryid" class="ts-form-control-light me-2">
                    <option disabled selected><?= $Localizer['CategorySelect'] ?></option>
                    <?php
                    if (isset($categories)){
                        $relations = null;
                        foreach ($categories as $category){
                            $relations[$category->It()->Id] = $category->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
                        }
                        asort($relations);
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>"><?= $relation; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Unit ID -->
            <div class="form-elt">
                <label for="unitid" class="me-2 text-end"><?= $Localizer['UnitId']; ?></label>
                <select id="unitid" name="unitid" class="ts-form-control-light me-2">
                    <option disabled selected><?= $Localizer['UnitSelect'] ?></option>
                    <?php
                    if (isset($units)){
                        $relations = null;
                        foreach ($units as $unit){
                            if ($unit->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId) == null) continue;
                            $relations[$unit->It()->Id] = $unit->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
                        }
                        asort($relations);
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>"><?= $relation; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Min stock -->
            <div class="form-elt">
                <label for="minstock" class="me-2 text-end"><?= $Localizer['MinStock']; ?></label>
                <input id="minstock" type="number" min="0" value="0" name="minstock" class="ts-form-control-light me-2"/>
            </div>
            <!-- Max stock -->
            <div class="form-elt">
                <label for="maxstock" class="me-2 text-end"><?= $Localizer['MaxStock']; ?></label>
                <input id="maxstock" type="number" min="0" value="0" name="maxstock" class="ts-form-control-light me-2"/>
            </div>
            <!-- Attribute ID -->
            <div class="form-elt">
                <label for="attributcheck" class="me-2 text-end"><?= $Localizer['AttributeCheck']; ?></label>
                <input id="attributcheck" type="checkbox" name="attributcheck" class="ts-form-control-light me-2">
            </div>
            <div class="form-elt">
                <label for="attributes" class="me-2 text-end"><?= $Localizer['ProductAttributes']; ?></label>
                <select id="attributes" name="attributes" multiple size="5" class="ts-form-control-light me-2">
                    <option value="0" disabled selected><?= $Localizer['AttributeSelect'] ?></option>
                    <?php
                    if (isset($attributes)){
                        $relations = null;
                        foreach ($attributes as $attribute){
                            if ($attribute->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId) == null) continue;
                            $relations[$attribute->It()->Id] = $attribute->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
                        }
                        asort($relations);
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>"><?= $relation; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Product locales -->
            <div class="form-elt">
                <label for="productlocalefr" class="me-2 text-end"><?= $Localizer['ProductLocaleFr']; ?></label>
                <input id="productlocalefr" type="text" name="productlocale[FR]" class="ts-form-control-light me-2"/>
            </div>
            <div class="form-elt">
                <label for="productlocaleus" class="me-2 text-end"><?= $Localizer['ProductLocaleUs']; ?></label>
                <input id="productlocaleus" type="text" name="productlocale[US]" class="ts-form-control-light me-2"/>
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