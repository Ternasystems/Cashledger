<?php
// View data
$components = $ViewData['components'];
$lang = $ViewData['CurrentLanguage'];
$categories = $ViewData['categories'];
$languages = $ViewData['languages'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewCategoryLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'CategoryName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Category', 'CategoryName'),
    'CategoryDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Category', 'CategoryDesc'),
    'CategorySuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Category', 'CategorySuccessBtn'),
    'CategoryList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Category', 'CategoryList')
    ];
?>
<div id="main">
    <form id="new-category" name="new-category" method="post" action="AddCategory">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavNewProductCategory'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Category name -->
            <div class="form-elt">
                <label for="categoryname" class="me-2 text-end"><?= $Localizer['CategoryName']; ?></label>
                <input id="categoryname" type="text" name="categoryname" class="ts-form-control-light me-2"/>
            </div>
            <!-- Category description -->
            <div class="form-elt">
                <label for="categorydesc" class="me-2 text-end"><?= $Localizer['CategoryDesc']; ?></label>
                <input id="categorydesc" type="text" name="categorydesc" class="ts-form-control-light me-2"/>
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
            sort($relations);
            foreach ($relations as $relation) {
                ?>
                <div class="category-elt"><?= $relation; ?></div>
                <?php
            }
        }
        ?>
    </div>
</div>