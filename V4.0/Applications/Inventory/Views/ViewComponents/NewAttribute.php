<?php
// View data
$components = $ViewData['components'];
$constraints = $ViewData['constraints'];
$lang = $ViewData['CurrentLanguage'];
$attributes = $ViewData['attributes'];
$languages = $ViewData['languages'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewAttributeLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'AttributeName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeName'),
    'AttributeType' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeType'),
    'AttributeLocaleFr' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeLocaleFr'),
    'AttributeLocaleUs' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeLocaleUs'),
    'ConstraintType' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'ConstraintType'),
    'AttributeConstraint' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeConstraint'),
    'AttributeDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeDesc'),
    'AttributeSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeSuccessBtn'),
    'AttributeList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Attribute', 'AttributeList')
];
?>
<div id="main">
    <form id="new-attribute" name="new-attribute" method="post" action="AddAttribute">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavNewProductAttribute'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Attribute name -->
            <div class="form-elt">
                <label for="attributename" class="me-2 text-end"><?= $Localizer['AttributeName']; ?></label>
                <input id="attributename" type="text" name="attributename" class="ts-form-control-light me-2"/>
            </div>
            <!-- Attribute type -->
            <div class="form-elt">
                <label for="attributetype" class="me-2 text-end"><?= $Localizer['AttributeType']; ?></label>
                <input id="attributetype" type="text" name="attributetype" class="ts-form-control-light me-2"/>
            </div>
            <!-- Attribute locales -->
            <div class="form-elt">
                <label for="attributelocalefr" class="me-2 text-end"><?= $Localizer['AttributeLocaleFr']; ?></label>
                <input id="attributelocalefr" type="text" name="attributelocale[FR]" class="ts-form-control-light me-2"/>
            </div>
            <div class="form-elt">
                <label for="attributelocaleus" class="me-2 text-end"><?= $Localizer['AttributeLocaleUs']; ?></label>
                <input id="attributelocaleus" type="text" name="attributelocale[US]" class="ts-form-control-light me-2"/>
            </div>
            <!-- Constraint type -->
            <div class="form-elt">
                <label for="constrainttype" class="me-2 text-end"><?= $Localizer['ConstraintType']; ?></label>
                <div class="constraint-type">
                    <?php
                    foreach ($constraints as $key => $constraint) {
                        ?>
                        <div class="d-flex align-items-center">
                            <input id="<?= $key ?>" name="constrainttype" type="radio" value="<?= $key ?>" class="ts-form-control-light me-1">
                            <label for="<?= $key ?>"><?= $constraint[$lang] ?></label>
                        </div>
                        <?php
                    }
                    ?>
                </div>
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
            sort($relations);
            foreach ($relations as $relation) {
                ?>
                <div class="attribute-elt"><?= $relation; ?></div>
                <?php
            }
        }
        ?>
    </div>
</div>