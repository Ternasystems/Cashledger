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
    'AttributeType' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeType'),
    'AttributeConstraint' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeConstraint'),
    'AttributeDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeDesc'),
    'AttributeSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeSuccessBtn'),
    'AttributeList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeList')
];
?>
<div id="main">
    <form id="modify-attribute" name="modify-attribute" method="post" action="UpdateAttribute">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavModifyProductAttribute'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Attribute name -->
            <div class="form-elt">
                <label for="attributename" class="me-2 text-end"><?= $Localizer['AttributeName']; ?></label>
                <input id="attributename" type="text" name="attributename" class="ts-form-control-light me-2"/>
                <input id="attributeid" type="hidden" name="attributeid" value="">
            </div>
            <!-- Attribute type -->
            <div class="form-elt">
                <label for="attributetype" class="me-2 text-end"><?= $Localizer['AttributeType']; ?></label>
                <input id="attributetype" type="text" name="attributetype" class="ts-form-control-light me-2"/>
            </div>
            <!-- Attribute constraint -->
            <div class="form-elt">
                <label for="attributeconstraint" class="me-2 text-end"><?= $Localizer['AttributeConstraint']; ?></label>
                <input id="attributeconstraint" type="text" name="attributeconstraint" class="ts-form-control-light me-2"/>
            </div>
            <!-- Attribute description -->
            <div class="form-elt">
                <label for="attributedesc" class="me-2 text-end"><?= $Localizer['AttributeDesc']; ?></label>
                <input id="attributedesc" type="text" name="attributedesc" class="ts-form-control-light me-2"/>
            </div>
            <!-- Attribute name -->
            <div class="form-elt">
                <button class="btn btn-success"><?= $Localizer['AttributeSuccessBtn'] ?></button>
            </div>
        </div>
    </form>
    <!-- -->
    <div id="attribute-list">
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
                <div class="attribute-elt ts-elt" data-form="#modify-attribute" data-id="<?= $key; ?>"><?= $relation ?></div>
                <?php
            }
        }
        ?>
    </div>
</div>