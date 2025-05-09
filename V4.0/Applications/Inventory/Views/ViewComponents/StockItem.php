<?php
$stocks = $ViewData['stocks'];
$lang = $ViewData['CurrentLanguage'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewStockDispatchLocale.xml');

$locales = new Locales();

$Localizer = [
    'NoStock' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'NoStock'),
    'StockAvailable' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'StockAvailable'),
    'StockId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'StockId'),
    'WarehouseId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'WarehouseId'),
    'PackagingId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'PackagingId')
];

if (isset($stocks)) {
    foreach ($stocks as $stock){
        ?>
        <div class="stock-item">
            <div data-class="stock-quantity">
                <span data-class="title"><?= $Localizer['StockAvailable']; ?></span>
                <span data-class="stock-value"><?= $stock->It()->Quantity; ?></span>
                <span data-class="stock-unit"><?= $stock->Unit()->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label; ?></span>
            </div>
            <div data-class="stock-id">
                <span data-class="title"><?= $Localizer['StockId']; ?></span>
                <span data-class="stock-value"><?= $stock->It()->Id; ?></span>
            </div>
            <div data-class="stock-warehouse">
                <span data-class="title"><?= $Localizer['WarehouseId']; ?></span>
                <span data-class="stock-value"><?= $stock->Warehouse()->It()->Name; ?></span>
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
<input type="hidden" name="StockItemModel" value="">
