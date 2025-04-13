<?php
// View data
$components = $ViewData['components'];
$lang = $ViewData['CurrentLanguage'];
$units = $ViewData['units'];
$languages = $ViewData['languages'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewUnitLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'UnitName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Unit', 'UnitName'),
    'UnitLabel' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Unit', 'UnitLabel'),
    'UnitLocaleFr' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Unit', 'UnitLocaleFr'),
    'UnitLocaleUs' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Unit', 'UnitLocaleUs'),
    'UnitDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Unit', 'UnitDesc'),
    'UnitSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Unit', 'UnitSuccessBtn'),
    'UnitList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Unit', 'UnitList')
];
?>
<div id="main">
    <form id="new-unit" name="new-unit" method="post" action="AddUnit">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavNewUnit'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Unit name -->
            <div class="form-elt">
                <label for="unitname" class="me-2 text-end"><?= $Localizer['UnitName']; ?></label>
                <input id="unitname" type="text" name="unitname" class="ts-form-control-light me-2"/>
            </div>
            <!-- Unit label -->
            <div class="form-elt">
                <label for="unitlabel" class="me-2 text-end"><?= $Localizer['UnitLabel']; ?></label>
                <input id="unitlabel" type="text" name="unitlabel" class="ts-form-control-light me-2"/>
            </div>
            <!-- Unit locales -->
            <div class="form-elt">
                <label for="unitlocalefr" class="me-2 text-end"><?= $Localizer['UnitLocaleFr']; ?></label>
                <input id="unitlocalefr" type="text" name="unitlocale[FR]" class="ts-form-control-light me-2"/>
            </div>
            <div class="form-elt">
                <label for="unitlocaleus" class="me-2 text-end"><?= $Localizer['UnitLocaleUs']; ?></label>
                <input id="unitlocaleus" type="text" name="unitlocale[US]" class="ts-form-control-light me-2"/>
            </div>
            <!-- Unit description -->
            <div class="form-elt">
                <label for="unitdesc" class="me-2 text-end"><?= $Localizer['UnitDesc']; ?></label>
                <input id="unitdesc" type="text" name="unitdesc" class="ts-form-control-light me-2"/>
            </div>
            <!-- Unit name -->
            <div class="form-elt">
                <button class="btn btn-success"><?= $Localizer['UnitSuccessBtn'] ?></button>
            </div>
        </div>
    </form>
    <!-- -->
    <div id="unit-list">
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
            sort($relations);
            foreach ($relations as $relation) {
                ?>
                <div class="unit-elt"><?= $relation; ?></div>
                <?php
            }
        }
        ?>
    </div>
</div>