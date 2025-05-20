<?php
$lang = $ViewData['CurrentLanguage'];
$inventories = $ViewData['inventories'];
$stocks = $ViewData['stocks'];
$products = $ViewData['products'];
$customers = $ViewData['customers'];
$units = $ViewData['units'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
$numberFormatter = new NumberFormatter($langId, NumberFormatter::DECIMAL);
$currencyFormatter = new NumberFormatter('fr-CM', NumberFormatter::CURRENCY);

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\DispatchItemLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'HeaderProduct' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'HeaderProduct'),
    'HeaderBatch' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'HeaderBatch'),
    'HeaderCustomer' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'HeaderCustomer'),
    'HeaderUnit' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'HeaderUnit'),
    'HeaderQty' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'HeaderQty'),
    'HeaderCost' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'HeaderCost'),
    'HeaderTotal' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'HeaderTotal'),
    'Maiden' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockOut', 'Maiden')
];
?>
<div id="dispatch-items">
    <!-- Header -->
    <div class="item-header fw-bold">
        <div><?= $Localizer['HeaderProduct']; ?></div>
        <div><?= $Localizer['HeaderBatch']; ?></div>
        <div><?= $Localizer['HeaderCustomer']; ?></div>
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
        $customerId = $inventory->It()->PartnerId;
        $customer = $customers->FirstOrDefault(fn($n) => $n->It()->Id == $customerId);
        $fullname = null;
        if (!empty($customer->Profile()->FullName()['MaidenName'])){
            $fullname = $customer->Profile()->FullName()['MaidenName'];
            if (!empty($customer->Profile()->FullName()['FirstName']))
                $fullname .= ', '.$customer->Profile()->FullName()['FirstName'];
            $fullname .= ' '.$Localizer['Maiden'].' '.$customer->Profile()->FullName()['LastName'];
        }else{
            $fullname = $customer->Profile()->FullName()['LastName'];
            if (!empty($customer->Profile()->FullName()['FirstName']))
                $fullname .= ', '.$customer->Profile()->FullName()['FirstName'];
        }
        ?>
        <div class="dispatch-item">
            <div><?= $label ?></div>
            <div><?= $stocks->FirstOrDefault(fn($n) => $n->It()->Id == $stockId)->It()->BatchNumber ?></div>
            <div><?= $fullname ?></div>
            <div><?= $unit ?></div>
            <div class="text-end"><?= $numberFormatter->format($inventory->It()->Quantity) ?></div>
            <div class="text-end"><?= $currencyFormatter->format($inventory->It()->UnitCost) ?></div>
            <div class="text-end"><?= $currencyFormatter->format($inventory->It()->UnitCost * $inventory->It()->Quantity) ?></div>
        </div>
        <?php
    }
    ?>
</div>