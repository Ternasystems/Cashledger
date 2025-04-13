<?php
// View data
$components = $ViewData['components'];
$lang = $ViewData['CurrentLanguage'];
$packagings = $ViewData['packagings'];
$languages = $ViewData['languages'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewPackagingLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'PackagingName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Packaging', 'PackagingName'),
    'PackagingLocaleFr' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Packaging', 'PackagingLocaleFr'),
    'PackagingLocaleUs' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Packaging', 'PackagingLocaleUs'),
    'PackagingDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Packaging', 'PackagingDesc'),
    'PackagingSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Packaging', 'PackagingSuccessBtn'),
    'PackagingList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Packaging', 'PackagingList')
];
?>
<div id="main">
    <form id="new-packaging" name="new-packaging" method="post" action="AddPackaging">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavNewPackaging'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Packaging name -->
            <div class="form-elt">
                <label for="packagingname" class="me-2 text-end"><?= $Localizer['PackagingName']; ?></label>
                <input id="packagingname" type="text" name="packagingname" class="ts-form-control-light me-2"/>
            </div>
            <!-- Packaging locales -->
            <div class="form-elt">
                <label for="packaginglocalefr" class="me-2 text-end"><?= $Localizer['PackagingLocaleFr']; ?></label>
                <input id="packaginglocalefr" type="text" name="packaginglocale[FR]" class="ts-form-control-light me-2"/>
            </div>
            <div class="form-elt">
                <label for="packaginglocaleus" class="me-2 text-end"><?= $Localizer['PackagingLocaleUs']; ?></label>
                <input id="packaginglocaleus" type="text" name="packaginglocale[US]" class="ts-form-control-light me-2"/>
            </div>
            <!-- Packaging description -->
            <div class="form-elt">
                <label for="packagingdesc" class="me-2 text-end"><?= $Localizer['PackagingDesc']; ?></label>
                <input id="packagingdesc" type="text" name="packagingdesc" class="ts-form-control-light me-2"/>
            </div>
            <!-- Packaging name -->
            <div class="form-elt">
                <button class="btn btn-success"><?= $Localizer['PackagingSuccessBtn'] ?></button>
            </div>
        </div>
    </form>
    <!-- -->
    <div id="packaging-list">
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
            sort($relations);
            foreach ($relations as $relation) {
                ?>
                <div class="packaging-elt"><?= $relation; ?></div>
                <?php
            }
        }
        ?>
    </div>
</div>