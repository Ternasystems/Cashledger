<?php
// View data
$components = $ViewData["components"];
$lang = $ViewData['CurrentLanguage'];
$packagings = $ViewData['packagings'];
$languages = $ViewData['languages'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\ModifyPackagingLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'PackagingName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Packaging', 'PackagingName'),
    'PackagingDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Packaging', 'PackagingDesc'),
    'PackagingSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Packaging', 'PackagingSuccessBtn'),
    'PackagingList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Packaging', 'PackagingList')
];
?>
<div id="main">
    <div id="packaging-delete-list">
        <div class="title">
            <span><?= $Localizer['PackagingList']; ?></span>
        </div>
        <?php
        if (isset($packagings)) {
            $langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
            $relations = null;
            foreach ($packagings as $packaging) {
                if ($packaging->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId) == null) continue;
                $relations[$packaging->It()->Id] = $packaging->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
            }
            asort($relations);
            foreach ($relations as $key => $relation) {
                ?>
                <div class="packaging-elt ts-elt d-flex justify-content-between" data-id="<?= $key; ?>">
                    <span><?= $relation; ?></span>
                    <span class="bi bi-trash"></span>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>