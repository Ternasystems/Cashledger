<?php
// View data
$dispatches = $ViewData["dispatches"];
$inventories = $ViewData["inventories"];
$lang = $ViewData['CurrentLanguage'];
$timezone = 'Africa/Douala';
$formatter = new IntlDateFormatter($lang, IntlDateFormatter::SHORT, IntlDateFormatter::SHORT, $timezone, IntlDateFormatter::GREGORIAN);
$currencyFormatter = new NumberFormatter('fr-CM', NumberFormatter::CURRENCY);

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\DispatchListLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'DispatchList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'DispatchList'),
    'DispatchDate' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'DispatchDate'),
    'DispatchNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'DispatchNumber'),
    'Reference' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'Reference'),
    'EditDate' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'EditDate'),
    'HeaderTotal' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'HeaderTotal')
];
?>
<div id="main">
    <div id="dispatch-list">
        <div class="title">
            <span><?= $Localizer['DispatchList']; ?></span>
        </div>
        <!-- Header -->
        <div class="form-header">
            <div><?= $Localizer['DispatchDate']; ?></div>
            <div><?= $Localizer['DispatchNumber']; ?></div>
            <div><?= $Localizer['Reference']; ?></div>
            <div><?= $Localizer['EditDate']; ?></div>
            <div class="text-end"><?= $Localizer['HeaderTotal']; ?></div>
        </div>
        <?php
        if (isset($dispatches)) {
            foreach ($dispatches as $dispatch) {
                $collection = $inventories->Where(fn($n) => $n->It()->NoteId == $dispatch->It()->Id && $n->It()->InventoryType->value == 'OUT');
                $total = 0;
                foreach ($collection as $item)
                    $total += $item->It()->UnitCost * $item->It()->Quantity;
                ?>
                <div class="form-row" data-id="<?= $dispatch->It()->Id ?>">
                    <div><?= $formatter->format($dispatch->It()->DispatchDate); ?></div>
                    <div><?= $dispatch->It()->DispatchNumber; ?></div>
                    <div><?= $dispatch->It()->Reference; ?></div>
                    <div><?= $formatter->format($dispatch->It()->EditDate); ?></div>
                    <div class="text-end"><?= $currencyFormatter->format($total) ?></div>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>