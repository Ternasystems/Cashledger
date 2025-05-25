<?php
// View data
$stock = $ViewData['stock'];
$inventories = $ViewData['inventories'];
$manufacturers = $ViewData['manufacturers'];
$lang = $ViewData['CurrentLanguage'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
$formatter = new IntlDateFormatter($lang, IntlDateFormatter::SHORT, IntlDateFormatter::SHORT, null, IntlDateFormatter::GREGORIAN);
$numberFormatter = new NumberFormatter($lang, NumberFormatter::DECIMAL);
$currencyFormatter = new NumberFormatter('fr-CM', NumberFormatter::CURRENCY);

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\StockDetailLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'StockId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'StockId'),
    'ProductLabel' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'ProductLabel'),
    'BatchNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'BatchNumber'),
    'StockDate' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'StockDate'),
    'LastChecked' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'LastChecked'),
    'Quantity' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'Quantity'),
    'MinStock' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'MinStock'),
    'MaxStock' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'MaxStock'),
    'Packaging' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'Packaging'),
    'Category' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'Category'),
    'Manufacturer' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'Manufacturer'),
    'Warehouse' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'Warehouse'),
    'InTitle' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'InTitle'),
    'OutTitle' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'OutTitle'),
    'InventTitle' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'InventTitle'),
    'Customer' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'Customer'),
    'Supplier' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'Supplier'),
    'Credential' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'Credential'),
    'DeliveryNote' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'DeliveryNote'),
    'DispatchNote' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'DispatchNote'),
    'ReturnNote' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'ReturnNote'),
    'WasteNote' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'WasteNote'),
    'TransferNote' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'TransferNote'),
    'InventNote' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'InventNote'),
    'Variation' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'Variation'),
    'UnitCost' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'UnitCost'),
    'Maiden' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Inventory', 'Maiden')
];

$product = $stock->Product()->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
$unit = $stock->Unit()->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
$packaging = $stock->Packaging()->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
$category = $stock->Product()->ProductCategory()->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
$productId = $stock->Product()->It()->Id;
$manufacturerId = $stock->Product()->ProductAttributes()
    ->FirstOrDefault(fn($n) => $n->It()->Name == 'Manufacturer')?->AttributeRelations()->FirstOrDefault(fn($t) => $t->ProductId == $productId)?->Value;
$manufacturer = is_null($manufacturerId) ? null : $manufacturers->FirstOrDefault(fn($n) => $n->It()->Id == $manufacturerId)->It()->Name;
?>
<div class="stock-elt">
    <span class="fw-bold"><?= $Localizer['StockId'] ?></span>
    <span><?= $stock->It()->Id ?></span>
</div>
<div class="stock-elt">
    <span class="fw-bold"><?= $Localizer['ProductLabel'] ?></span>
    <span><?= $product ?></span>
</div>
<div class="stock-elt">
    <span class="fw-bold"><?= $Localizer['StockDate'] ?></span>
    <span><?= $formatter->format($stock->It()->StockDate) ?></span>
</div>
<div class="stock-elt">
    <span class="fw-bold"><?= $Localizer['LastChecked'] ?></span>
    <span><?= $formatter->format($stock->It()->LastChecked) ?></span>
</div>
<div class="stock-elt">
    <span class="fw-bold"><?= $Localizer['Category'] ?></span>
    <span><?= $category ?></span>
</div>
<div class="stock-elt">
    <span class="fw-bold"><?= $Localizer['Manufacturer'] ?></span>
    <span><?= $manufacturer ?></span>
</div>
<div class="stock-elt">
    <span class="fw-bold"><?= $Localizer['Warehouse'] ?></span>
    <span><?= $stock->Warehouse()->It()->Name ?></span>
</div>
<div class="stock-elt">
    <span class="fw-bold"><?= $Localizer['BatchNumber'] ?></span>
    <span><?= $stock->It()->BatchNumber ?></span>
</div>
<div class="stock-elt">
    <span class="fw-bold"><?= $Localizer['Packaging'] ?></span>
    <span><?= $packaging ?></span>
</div>
<div class="stock-elt">
    <span class="fw-bold"><?= $Localizer['Quantity'] ?></span>
    <span data-id="stock"><?= $numberFormatter->format($stock->It()->Quantity).' '.$unit ?></span>
</div>
<div class="stock-elt">
    <span class="fw-bold"><?= $Localizer['MinStock'] ?></span>
    <span data-id="min-stock"><?= $numberFormatter->format($stock->Product()->It()->MinStock).' '.$unit ?></span>
</div>
<div class="stock-elt">
    <span class="fw-bold"><?= $Localizer['MaxStock'] ?></span>
    <span data-id="max-stock"><?= $numberFormatter->format($stock->Product()->It()->MaxStock).' '.$unit ?></span>
</div>
<div id="inventory">
    <div id="stock-input">
        <div class="ts-title">
            <span><?= $Localizer['InTitle'] ?></span>
        </div>
        <?php
        $inputs = $inventories->Where(fn($n) => $n->It()->InventoryType->value == 'IN' && $n->It()->StockId == $stock->It()->Id);
        foreach ($inputs as $input){
            ?>
            <div class="invent-elt">
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['DeliveryNote'] ?></span>
                    <span><?= $input->Note()->It()->DeliveryNumber ?></span>
                </div>
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['StockDate'] ?></span>
                    <span><?= $formatter->format($input->It()->InventDate) ?></span>
                </div>
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['Quantity'] ?></span>
                    <span><?= $numberFormatter->format($input->It()->Quantity).' '.$unit ?></span>
                </div>
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['UnitCost'] ?></span>
                    <span><?= $currencyFormatter->format($input->It()->UnitCost) ?></span>
                </div>
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['Supplier'] ?></span>
                    <span><?= $input->Partner()->Profile()->It()->LastName ?></span>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
    <!-- -->
    <div id="stock-output">
        <div class="ts-title">
            <span><?= $Localizer['OutTitle'] ?></span>
        </div>
        <?php
        $outputs = $inventories->Where(fn($n) => $n->It()->InventoryType->value == 'OUT' && $n->It()->StockId == $stock->It()->Id);
        foreach ($outputs as $output){
            $fullname = null;
            $customer = $output->Partner();
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
            <div class="invent-elt">
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['DispatchNote'] ?></span>
                    <span><?= $output->Note()->It()->DispatchNumber ?></span>
                </div>
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['StockDate'] ?></span>
                    <span><?= $formatter->format($output->It()->InventDate) ?></span>
                </div>
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['Quantity'] ?></span>
                    <span><?= $numberFormatter->format($output->It()->Quantity).' '.$unit ?></span>
                </div>
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['UnitCost'] ?></span>
                    <span><?= $currencyFormatter->format($output->It()->UnitCost) ?></span>
                </div>
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['Customer'] ?></span>
                    <span><?= $fullname ?></span>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
    <!-- -->
    <div id="stock-invent">
        <div class="ts-title">
            <span><?= $Localizer['InventTitle'] ?></span>
        </div>
        <?php
        $outputs = $inventories->Where(fn($n) => $n->It()->InventoryType->value == 'INVENT' && $n->It()->StockId == $stock->It()->Id);
        foreach ($outputs as $output){
            $fullname = null;
            $credential = $output->Partner();
            if (!empty($credential->Profile()->FullName()['MaidenName'])){
                $fullname = $credential->Profile()->FullName()['MaidenName'];
                if (!empty($credential->Profile()->FullName()['FirstName']))
                    $fullname .= ', '.$credential->Profile()->FullName()['FirstName'];
                $fullname .= ' '.$Localizer['Maiden'].' '.$credential->Profile()->FullName()['LastName'];
            }else{
                $fullname = $credential->Profile()->FullName()['LastName'];
                if (!empty($credential->Profile()->FullName()['FirstName']))
                    $fullname .= ', '.$credential->Profile()->FullName()['FirstName'];
            }
            ?>
            <div class="invent-elt">
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['InventNote'] ?></span>
                    <span><?= $output->Note()->It()->InventNumber ?></span>
                </div>
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['StockDate'] ?></span>
                    <span><?= $formatter->format($output->It()->InventDate) ?></span>
                </div>
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['Variation'] ?></span>
                    <span><?= $numberFormatter->format($output->It()->Quantity * ($output->It()->Quantity == 0 ? 1 : -1)).' '.$unit ?></span>
                </div>
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['UnitCost'] ?></span>
                    <span><?= $currencyFormatter->format($output->It()->UnitCost) ?></span>
                </div>
                <div class="stock-elt">
                    <span class="fw-bold"><?= $Localizer['Credential'] ?></span>
                    <span><?= $fullname ?></span>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
