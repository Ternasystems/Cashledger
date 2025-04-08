<?php
// View data
$components = $ViewData["components"];
$lang = $ViewData['CurrentLanguage'];
$units = $ViewData['units'];
$languages = $ViewData['languages'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\ModifyUnitLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'UnitName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Unit', 'UnitName'),
    'UnitLabel' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Unit', 'UnitLabel'),
    'UnitDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Unit', 'UnitDesc'),
    'UnitSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Unit', 'UnitSuccessBtn'),
    'UnitList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Unit', 'UnitList')
];
?>
<div id="main">
    <div id="unit-delete-list">
        <div class="title">
            <span><?= $Localizer['UnitList']; ?></span>
        </div>
        <?php
        if (isset($units)) {
            $langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
            $relations = null;
            foreach ($units as $unit) {
                if ($unit->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId) == null) continue;
                $relations[$unit->It()->Id] = $unit->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
            }
            asort($relations);
            foreach ($relations as $key => $relation) {
                ?>
                <div class="unit-elt ts-elt d-flex justify-content-between" data-id="<?= $key; ?>">
                    <span><?= $relation; ?></span>
                    <span class="bi bi-trash"></span>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>