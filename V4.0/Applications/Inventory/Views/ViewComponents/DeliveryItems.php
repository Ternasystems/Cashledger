<?php
$lang = $ViewData['CurrentLanguage'];
$inventories = $ViewData['inventories'];
$stocks = $ViewData['stocks'];
$products = $ViewData['products'];
$units = $ViewData['units'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
$numberFormatter = new NumberFormatter($langId, NumberFormatter::DECIMAL);
$currencyFormatter = new NumberFormatter('fr-CM', NumberFormatter::CURRENCY);

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\DeliveryItemLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'HeaderProduct' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'HeaderProduct'),
    'HeaderBatch' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'HeaderBatch'),
    'HeaderUnit' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'HeaderUnit'),
    'HeaderQty' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'HeaderQty'),
    'HeaderCost' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'HeaderCost'),
    'HeaderTotal' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'HeaderTotal')
];
?>
<div id="delivery-items">
    <!-- Header -->
    <div class="item-header fw-bold">
        <div><?= $Localizer['HeaderProduct']; ?></div>
        <div><?= $Localizer['HeaderBatch']; ?></div>
        <div><?= $Localizer['HeaderUnit']; ?></div>
        <div class="text-end"><?= $Localizer['HeaderQty']; ?></div>
        <div class="text-end"><?= $Localizer['HeaderCost']; ?></div>
        <div class="text-end"><?= $Localizer['HeaderTotal']; ?></div>
    </div>
    <?php
    foreach ($inventories as $inventory) {
        $stockId = $inventory->It()->StockId;
        $productId = $stocks->FirstOrDefault(fn($n) => $n->It()->Id == $stockId)->It()->ProductId;
        $product = $products->FirstOrDefault(fn($n) => $n->It()->Id == $productId);
        $label = $product->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
        $unitId = $stocks->FirstOrDefault(fn($n) => $n->It()->Id == $stockId)->It()->UnitId;
        $unit = $units->FirstOrDefault(fn($n) => $n->It()->Id == $unitId)->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
        ?>
    <div class="delivery-item">
        <div><?= $label ?></div>
        <div><?= $stocks->FirstOrDefault(fn($n) => $n->It()->Id == $stockId)->It()->BatchNumber ?></div>
        <div><?= $unit ?></div>
        <div class="text-end"><?= $numberFormatter->format($inventory->It()->Quantity) ?></div>
        <div class="text-end"><?= $currencyFormatter->format($inventory->It()->UnitCost) ?></div>
        <div class="text-end"><?= $currencyFormatter->format($inventory->It()->UnitCost * $inventory->It()->Quantity) ?></div>
    </div>
    <?php
    }
    ?>
</div>