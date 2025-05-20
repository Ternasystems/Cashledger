<?php
$stocks = $ViewData['stocks'];
$lang = $ViewData['CurrentLanguage'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewStockInventoryLocale.xml');

$locales = new Locales();

$Localizer = [
    'NoStock' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'NoStock'),
    'StockAvailable' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'StockAvailable'),
    'StockId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'StockId'),
    'ProductId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'ProductId'),
    'PackagingId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'PackagingId'),
    'BatchNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'BatchNumber')
];

if (isset($stocks)) {
    foreach ($stocks as $stock){
        ?>
        <div class="stock-item">
            <div data-class="stock-id">
                <span data-class="title"><?= $Localizer['StockId']; ?></span>
                <span data-class="stock-value"><?= $stock->It()->Id; ?></span>
            </div>
            <div data-class="stock-batchnumber">
                <span data-class="title"><?= $Localizer['BatchNumber']; ?></span>
                <span data-class="stock-value"><?= $stock->It()->BatchNumber; ?></span>
            </div>
            <div data-class="stock-product">
                <span data-class="title"><?= $Localizer['ProductId']; ?></span>
                <span data-class="stock-value"><?= $stock->Product()->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label; ?></span>
            </div>
            <div data-class="stock-quantity">
                <span data-class="title"><?= $Localizer['StockAvailable']; ?></span>
                <span data-class="stock-value"><?= $stock->It()->Quantity; ?></span>
                <span data-class="stock-unit"><?= $stock->Unit()->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label; ?></span>
            </div>
            <div data-class="stock-packaging">
                <span data-class="title"><?= $Localizer['PackagingId']; ?></span>
                <span data-class="stock-value"><?= $stock->Packaging()->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label; ?></span>
            </div>
            <span class="d-none"><?= htmlspecialchars(json_encode($stock->It()), ENT_QUOTES); ?></span>
        </div>
        <?php
    }
}
else{
    ?>
    <div id="no-stock" class="stock-item"><?= $Localizer['NoStock']; ?></div>
    <?php
}
?>
<input type="hidden" name="InventStockModel" value="">
