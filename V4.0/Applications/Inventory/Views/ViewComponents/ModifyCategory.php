<?php
// View data
$components = $ViewData["components"];
$lang = $ViewData['CurrentLanguage'];
$categories = $ViewData['categories'];
$languages = $ViewData['languages'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\ModifyCategoryLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'CategoryName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Category', 'CategoryName'),
    'CategoryDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Category', 'CategoryDesc'),
    'CategoryLocaleFr' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Category', 'CategoryLocaleFr'),
    'CategoryLocaleUs' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Category', 'CategoryLocaleUs'),
    'CategorySuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Category', 'CategorySuccessBtn'),
    'CategoryList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Category', 'CategoryList')
];
?>
<div id="main">
    <form id="modify-category" name="modify-category" method="post" action="UpdateCategory">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavModifyProductCategory'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Category name -->
            <div class="form-elt">
                <label for="categoryname" class="me-2 text-end"><?= $Localizer['CategoryName']; ?></label>
                <input id="categoryname" type="text" name="categoryname" class="ts-form-control-light me-2"/>
                <input id="categoryid" type="hidden" name="categoryid" value="">
            </div>
            <!-- Category description -->
            <div class="form-elt">
                <label for="categorydesc" class="me-2 text-end"><?= $Localizer['CategoryDesc']; ?></label>
                <input id="categorydesc" type="text" name="categorydesc" class="ts-form-control-light me-2"/>
            </div>
            <!-- Category locales -->
            <div class="form-elt">
                <label for="categorylocalefr" class="me-2 text-end"><?= $Localizer['CategoryLocaleFr']; ?></label>
                <input id="categorylocalefr" type="text" name="categorylocale[FR]" class="ts-form-control-light me-2"/>
            </div>
            <div class="form-elt">
                <label for="categorylocaleus" class="me-2 text-end"><?= $Localizer['CategoryLocaleUs']; ?></label>
                <input id="categorylocaleus" type="text" name="categorylocale[US]" class="ts-form-control-light me-2"/>
            </div>
            <!-- Category name -->
            <div class="form-elt">
                <button class="btn btn-success"><?= $Localizer['CategorySuccessBtn'] ?></button>
            </div>
        </div>
    </form>
    <!-- -->
    <div id="category-list">
        <div class="title">
            <span><?= $Localizer['CategoryList']; ?></span>
        </div>
        <?php
        if (isset($categories)) {
            $langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
            $relations = null;
            foreach ($categories as $category) {
                $relations[$category->It()->Id] = $category->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
            }
            asort($relations);
            foreach ($relations as $key => $relation) {
                ?>
                <div class="category-elt ts-elt" data-form="#modify-category" data-id="<?= $key; ?>"><?= $relation ?></div>
                <?php
            }
        }
        ?>
    </div>
</div>