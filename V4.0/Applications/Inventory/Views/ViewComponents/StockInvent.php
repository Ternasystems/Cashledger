<?php
// View data
$stocks = $ViewData['stocks'];
$lang = $ViewData['CurrentLanguage'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
$timezone = 'Africa/Douala';
$formatter = new IntlDateFormatter($lang, IntlDateFormatter::SHORT, IntlDateFormatter::SHORT, $timezone, IntlDateFormatter::GREGORIAN);
$currencyFormatter = new NumberFormatter('fr-CM', NumberFormatter::CURRENCY);
$numberFormatter = new NumberFormatter($lang, NumberFormatter::DECIMAL);

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\StockInventLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'StockList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'StockList'),
    'StockDetails' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'StockDetails'),
    'StockId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'StockId'),
    'ProductName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'ProductName'),
    'Warehouse' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'Warehouse'),
    'BatchNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'BatchNumber'),
    'StockDate' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'StockDate'),
    'LastChecked' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'LastChecked'),
    'Quantity' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'Quantity'),
    'UnitCost' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'UnitCost'),
    'TotalCost' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'TotalCost')
];
?>
<div id="main">
    <div id="stock-list">
        <div class="title">
            <span><?= $Localizer['StockList']; ?></span>
        </div>
        <!-- Header -->
        <div class="form-header">
            <div><?= $Localizer['StockId']; ?></div>
            <div><?= $Localizer['ProductName']; ?></div>
            <div><?= $Localizer['BatchNumber']; ?></div>
            <div><?= $Localizer['StockDate']; ?></div>
            <div><?= $Localizer['LastChecked']; ?></div>
            <div class="text-end"><?= $Localizer['Quantity']; ?></div>
            <div class="text-end"><?= $Localizer['UnitCost']; ?></div>
            <div class="text-end"><?= $Localizer['TotalCost']; ?></div>
        </div>
        <?php
        if (isset($stocks)) {
            $total = 0;
            $stocks->SortBy(fn($n) => $n->It()->Id);
            foreach ($stocks as $stock) {
                $total = $stock->It()->Quantity * $stock->It()->UnitCost;
                $label = $stock->Product()->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
                ?>
                <div class="form-row" data-id="<?= $stock->It()->Id ?>">
                    <div><?= $stock->It()->Id ?></div>
                    <div><?= $label ?></div>
                    <div><?= $stock->It()->BatchNumber ?></div>
                    <div><?= $formatter->format($stock->It()->StockDate) ?></div>
                    <div><?= $formatter->format($stock->It()->LastChecked) ?></div>
                    <div class="text-end"><?= $numberFormatter->format($stock->It()->Quantity) ?></div>
                    <div class="text-end"><?= $currencyFormatter->format($stock->It()->UnitCost) ?></div>
                    <div class="text-end"><?= $currencyFormatter->format($total) ?></div>
                </div>
        <?php
            }
        }
        ?>
    </div>
    <!-- -->
    <div id="stock-details">
        <div class="title">
            <span><?= $Localizer['StockDetails']; ?></span>
        </div>
        <div id="stock-detail">
        </div>
    </div>
</div>
