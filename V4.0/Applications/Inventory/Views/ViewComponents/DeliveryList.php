<?php
// View data
$deliveries = $ViewData["deliveries"];
$inventories = $ViewData["inventories"];
$lang = $ViewData['CurrentLanguage'];
$timezone = 'Africa/Douala';
$formatter = new IntlDateFormatter($lang, IntlDateFormatter::SHORT, IntlDateFormatter::SHORT, $timezone, IntlDateFormatter::GREGORIAN);
$currencyFormatter = new NumberFormatter('fr-CM', NumberFormatter::CURRENCY);

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\DeliveryListLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'DeliveryList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'DeliveryList'),
    'DeliveryDate' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'DeliveryDate'),
    'DeliveryNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'DeliveryNumber'),
    'Reference' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'Reference'),
    'EditDate' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'EditDate'),
    'HeaderTotal' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockIn', 'HeaderTotal')
];
?>
<div id="main">
    <div id="delivery-list">
        <div class="title">
            <span><?= $Localizer['DeliveryList']; ?></span>
        </div>
        <!-- Header -->
        <div class="form-header">
            <div><?= $Localizer['DeliveryDate']; ?></div>
            <div><?= $Localizer['DeliveryNumber']; ?></div>
            <div><?= $Localizer['Reference']; ?></div>
            <div><?= $Localizer['EditDate']; ?></div>
            <div class="text-end"><?= $Localizer['HeaderTotal']; ?></div>
        </div>
        <?php
        if (isset($deliveries)) {
            foreach ($deliveries as $delivery) {
                $collection = $inventories->Where(fn($n) => $n->It()->NoteId == $delivery->It()->Id && $n->It()->InventoryType->value == 'IN');
                $total = 0;
                foreach ($collection as $item)
                    $total += $item->It()->UnitCost * $item->It()->Quantity;
            ?>
                <div class="form-row" data-id="<?= $delivery->It()->Id ?>">
                    <div><?= $formatter->format($delivery->It()->DeliveryDate); ?></div>
                    <div><?= $delivery->It()->DeliveryNumber; ?></div>
                    <div><?= $delivery->It()->Reference; ?></div>
                    <div><?= $formatter->format($delivery->It()->EditDate); ?></div>
                    <div class="text-end"><?= $currencyFormatter->format($total) ?></div>
                </div>
            <?php
            }
        }
        ?>
    </div>
</div>