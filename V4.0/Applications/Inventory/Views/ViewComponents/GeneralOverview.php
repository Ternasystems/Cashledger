<?php
$components = $ViewData['components'];
$lang = $ViewData['CurrentLanguage'];
$warehouses = $ViewData['warehouses'];
$products = $ViewData['products'];
$stocks = $ViewData['stocks'];
$inventories = $ViewData['inventories'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
$dateFormatter = new IntlDateFormatter($lang, IntlDateFormatter::SHORT, IntlDateFormatter::NONE, null, IntlDateFormatter::GREGORIAN,
    'dd/MM');
$dteFormatter = new IntlDateFormatter($lang, IntlDateFormatter::FULL, IntlDateFormatter::NONE, null, IntlDateFormatter::GREGORIAN);
$numberFormatter = new NumberFormatter($lang, NumberFormatter::DECIMAL);

$today = new DateTime('2025-05-21');
$yesterday = (new DateTime('2025-05-21'))->modify('-1 day');
$week = (new DateTime('2025-05-21'))->modify('-7 days');
$month = (new DateTime('2025-05-21'))->modify('-1 month');
$year = (new DateTime('2025-05-21'))->modify('-1 year');

$inCollection = $inventories->Where(fn($n) => $n->It()->InventoryType->value == 'IN' || $n->It()->InventoryType->value == 'RETURN');
$outCollection = $inventories->Where(fn($n) => $n->It()->InventoryType->value == 'OUT' || $n->It()->InventoryType->value == 'WASTE');
$invCollection = $inventories->Where(fn($n) => $n->It()->InventoryType->value == 'INVENT');

$delCollection = $inventories->Where(fn($n) => $n->It()->InventoryType->value == 'IN');
$disCollection = $inventories->Where(fn($n) => $n->It()->InventoryType->value == 'OUT');
$retCollection = $inventories->Where(fn($n) => $n->It()->InventoryType->value == 'RETURN');
$wstCollection = $inventories->Where(fn($n) => $n->It()->InventoryType->value == 'WASTE');

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\GeneralOverviewLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'Label' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Label'),
    'Delivery' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Delivery'),
    'Dispatch' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Dispatch'),
    'Inventory' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Inventory'),
    'Today' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Today'),
    'Yesterday' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Yesterday'),
    'Variation' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Variation'),
    'Threshold' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Threshold'),
    'MinThreshold' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'MinThreshold'),
    'MaxThreshold' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'MaxThreshold'),
    'Return' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Return'),
    'Waste' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Waste'),
    'Week' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Week'),
    'Month' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Month'),
    'Year' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Year'),
    'Trimester' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Trimester'),
    'Semester' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'GeneralOverview', 'Semester')
];
?>
<div id="main">
    <!-- Warehouses -->
    <div id="warehouse-cards" class="card">
        <!--Title-->
        <div class="card-header fw-bold text-bg-dark"><?= $components['NavWareHouse'][$lang]['title']; ?></div>
        <div class="card-body">
            <?php
            if (isset($warehouses)){
                // Last inventory
                ?>
                <div id="warehouse-last-hd" class="ts-hd">
                    <div><?= $Localizer['Label'] ?></div>
                    <div><?= $Localizer['Inventory'] ?></div>
                </div>
                <?php
                foreach ($warehouses as $warehouse){
                    $id = $warehouse->It()->Id;
                    $collection = $stocks->Where(fn($n) => $n->It()->WarehouseId == $id && $invCollection->Where(fn($t) => $t->It()->StockId == $n->It()->Id));
                    if ($collection->count() == 0) continue;
                    $dte = null;
                    foreach ($collection as $stock){
                        $coll = $invCollection->Where(fn($n) => $n->It()->StockId == $stock->It()->Id);
                        foreach ($coll as $inventory){
                            if (is_null($dte) || $inventory->It()->InventDate > $dte) $dte = $inventory->It()->InventDate;
                        }
                    }
                    ?>
                    <div id="warehouse-last-bd" class="ts-bd">
                        <div><?= $warehouse->It()->Name ?></div>
                        <div><?= $dteFormatter->format($dte) ?></div>
                    </div>
                    <?php
                }
                // Deliveries and returns
                ?>
                <div id="warehouse-delivery-hd" class="ts-hd">
                    <div><?= $Localizer['Delivery'] ?></div>
                    <div><?= $Localizer['Label'] ?></div>
                    <div><?= $Localizer['Today'] ?></div>
                    <div><?= $Localizer['Yesterday'] ?></div>
                    <div><?= $Localizer['Variation'] ?></div>
                    <div class="tday-hd"><?= $dateFormatter->format($today) ?></div>
                    <div class="yday-hd"><?= $dateFormatter->format($yesterday) ?></div>
                </div>
                <?php
                foreach ($warehouses as $warehouse){
                    $id = $warehouse->It()->Id;
                    $collection = $stocks->Where(fn($n) => $n->It()->WarehouseId == $id && $inCollection->Where(fn($t) => $t->It()->StockId == $n->It()->Id));
                    if ($collection->count() == 0) continue;
                    $t = 0;
                    $y = 0;
                    $v = 0;
                    foreach ($collection as $stock){
                        $t += $inCollection->Where(fn($n) => $n->It()->StockId == $stock->It()->Id &&
                            $n->It()->InventDate->format('Y-m-d') == $today->format('Y-m-d'))->count();
                        $y += $inCollection->Where(fn($n) => $n->It()->StockId == $stock->It()->Id &&
                            $n->It()->InventDate->format('Y-m-d') == $yesterday->format('Y-m-d'))->count();
                        $v = $t - $y;
                    }
                    ?>
                    <div id="warehouse-delivery-bd" class="ts-bd">
                        <div><?= $warehouse->It()->Name ?></div>
                        <div><?= $numberFormatter->format($t) ?></div>
                        <div><?= $numberFormatter->format($y) ?></div>
                        <div><?= $numberFormatter->format($v) ?></div>
                    </div>
                    <?php
                }
                // Dispatches and wastes
                ?>
                <div id="warehouse-dispatch-hd" class="ts-hd">
                    <div><?= $Localizer['Dispatch'] ?></div>
                    <div><?= $Localizer['Label'] ?></div>
                    <div><?= $Localizer['Today'] ?></div>
                    <div><?= $Localizer['Yesterday'] ?></div>
                    <div><?= $Localizer['Variation'] ?></div>
                    <div class="tday-hd"><?= $dateFormatter->format($today) ?></div>
                    <div class="yday-hd"><?= $dateFormatter->format($yesterday) ?></div>
                </div>
                <?php
                foreach ($warehouses as $warehouse){
                    $id = $warehouse->It()->Id;
                    $collection = $stocks->Where(fn($n) => $n->It()->WarehouseId == $id && $outCollection->Where(fn($t) => $t->It()->StockId == $n->It()->Id));
                    if ($collection->count() == 0) continue;
                    $t = 0;
                    $y = 0;
                    $v = 0;
                    foreach ($collection as $stock){
                        $t += $outCollection->Where(fn($n) => $n->It()->StockId == $stock->It()->Id &&
                            $n->It()->InventDate->format('Y-m-d') == $today->format('Y-m-d'))->count();
                        $y += $outCollection->Where(fn($n) => $n->It()->StockId == $stock->It()->Id &&
                            $n->It()->InventDate->format('Y-m-d') == $yesterday->format('Y-m-d'))->count();
                        $v = $t - $y;
                    }
                    ?>
                    <div id="warehouse-delivery-bd" class="ts-bd">
                        <div><?= $warehouse->It()->Name ?></div>
                        <div><?= $numberFormatter->format($t) ?></div>
                        <div><?= $numberFormatter->format($y) ?></div>
                        <div><?= $numberFormatter->format($v) ?></div>
                    </div>
                    <?php
                }
            }
            ?>
        </div>
    </div>
    <!-- Products -->
    <div id="product-cards" class="card">
        <!--Title-->
        <div class="card-header fw-bold text-bg-dark"><?= $components['NavProduct'][$lang]['title']; ?></div>
        <div class="card-body">
            <?php
            if (isset($products)){
                // Thresholds
                ?>
                <div id="product-threshold-hd" class="ts-hd">
                    <div><?= $Localizer['Threshold'] ?></div>
                    <div><?= $Localizer['Label'] ?></div>
                    <div><?= $Localizer['MinThreshold'] ?></div>
                    <div><?= $Localizer['MaxThreshold'] ?></div>
                </div>
                <?php
                $stockProducts = $stocks->Where(fn($n) => $n->It()->Quantity <= $n->Product()->It()->MinStock || $n->It()->Quantity >= $n->Product()->It()->MaxStock)
                    ?->Select(fn($n) => $n->Product());
                if ($stockProducts->count() > 0){
                    foreach ($stockProducts as $product){
                        $id = $product->It()->Id;
                        $label = $product->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
                        //
                        $min = $product->It()->MinStock;
                        $max = $product->It()->MaxStock;
                        $quantity = $stocks->Where(fn($n) => $n->It()->ProductId == $id)->Sum(fn($n) => $n->It()->Quantity);
                        ?>
                        <div id="product-threshold-bd" class="ts-bd">
                            <div><?= $label ?></div>
                            <div class="text-end"><?= $quantity <= $min ? $numberFormatter->format($quantity - $min) : 'N/A' ?></div>
                            <div class="text-end"><?= $quantity >= $max ? $numberFormatter->format($quantity - $max) : 'N/A' ?></div>
                        </div>
                        <?php
                    }
                }
                // Best input inventories
                ?>
                <div id="product-delivery-hd" class="ts-hd">
                    <div><?= $Localizer['Delivery'] ?></div>
                    <div><?= $Localizer['Label'] ?></div>
                    <div><?= $Localizer['Week'] ?></div>
                    <div><?= $Localizer['Month'] ?></div>
                    <div><?= $Localizer['Year'] ?></div>
                </div>
                <?php
                // Best output inventories
                ?>
                <div id="product-dispatch-hd" class="ts-hd">
                    <div><?= $Localizer['Dispatch'] ?></div>
                    <div><?= $Localizer['Label'] ?></div>
                    <div><?= $Localizer['Week'] ?></div>
                    <div><?= $Localizer['Month'] ?></div>
                    <div><?= $Localizer['Year'] ?></div>
                </div>
                <?php
                // Best return inventories
                ?>
                <div id="product-return-hd" class="ts-hd">
                    <div><?= $Localizer['Return'] ?></div>
                    <div><?= $Localizer['Label'] ?></div>
                    <div><?= $Localizer['Week'] ?></div>
                    <div><?= $Localizer['Month'] ?></div>
                    <div><?= $Localizer['Year'] ?></div>
                </div>
                <?php
                // Best waste inventories
                ?>
                <div id="product-waste-hd" class="ts-hd">
                    <div><?= $Localizer['Waste'] ?></div>
                    <div><?= $Localizer['Label'] ?></div>
                    <div><?= $Localizer['Week'] ?></div>
                    <div><?= $Localizer['Month'] ?></div>
                    <div><?= $Localizer['Year'] ?></div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <!-- Customers -->
    <div id="customer-cards" class="card">
        <!--Title-->
        <div class="card-header fw-bold text-bg-dark"><?= $components['NavCustomer'][$lang]['title']; ?></div>
        <div class="card-body">
        </div>
    </div>
    <!-- Suppliers -->
    <div id="supplier-cards" class="card">
        <!--Title-->
        <div class="card-header fw-bold text-bg-dark"><?= $components['NavSupplier'][$lang]['title']; ?></div>
        <div class="card-body">
        </div>
    </div>
</div>