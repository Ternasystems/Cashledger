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
    'CategorySuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Category', 'CategorySuccessBtn'),
    'CategoryList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Category', 'CategoryList')
];
?>
<div id="main">
    <div id="category-delete-list">
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
                <div class="category-elt ts-elt d-flex justify-content-between" data-id="<?= $key; ?>">
                    <span><?= $relation; ?></span>
                    <span class="bi bi-trash"></span>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>