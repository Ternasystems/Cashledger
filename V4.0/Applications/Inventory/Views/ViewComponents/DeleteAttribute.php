<?php
// View data
$components = $ViewData["components"];
$lang = $ViewData['CurrentLanguage'];
$attributes = $ViewData['attributes'];
$languages = $ViewData['languages'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\ModifyAttributeLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'AttributeName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeName'),
    'AttributeDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeDesc'),
    'AttributeSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeSuccessBtn'),
    'AttributeList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeList')
];
?>
<div id="main">
    <div id="attribute-delete-list">
        <div class="title">
            <span><?= $Localizer['AttributeList']; ?></span>
        </div>
        <?php
        if (isset($attributes)) {
            $langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
            $relations = null;
            foreach ($attributes as $attribute) {
                $relations[$attribute->It()->Id] = $attribute->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
            }
            asort($relations);
            foreach ($relations as $key => $relation) {
                ?>
                <div class="attribute-elt ts-elt d-flex justify-content-between" data-id="<?= $key; ?>">
                    <span><?= $relation; ?></span>
                    <span class="bi bi-trash"></span>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>