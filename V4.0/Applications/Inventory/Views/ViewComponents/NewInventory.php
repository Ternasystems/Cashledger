<?php
// View data
$warehouses = $ViewData['warehouses'];
$components = $ViewData['components'];
$lang = $ViewData['CurrentLanguage'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewStockInventoryLocale.xml');

$locales = new Locales();

$Localizer = [
    'InventoryNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'InventoryNumber'),
    'InventoryDate' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'InventoryDate'),
    'WarehouseId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'WarehouseId'),
    'WarehouseSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'WarehouseSelect'),
    'StockQuantity' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'StockQuantity'),
    'StockAvailable' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'StockAvailable'),
    'StockId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'StockId'),
    'ProductId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'ProductId'),
    'PackagingId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'PackagingId'),
    'InventoryDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'InventoryDesc'),
    'InventoryAddBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'InventoryAddBtn'),
    'HeaderNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'HeaderNumber'),
    'HeaderTotal' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'HeaderTotal'),
    'HeaderStock' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'HeaderStock'),
    'BatchNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'BatchNumber'),
    'UnitId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'UnitId'),
    'CredentialId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'CredentialId'),
    'Maiden' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'Maiden'),
    'StockVariance' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'StockVariance'),
    'InventorySuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockInventory', 'InventorySuccessBtn')
];
?>
<div id="main" data-id="new-stockinventory">
    <form id="new-inventory" name="new-inventory" method="post" action="AddInventory">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavNewInventory'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Inventory number -->
            <div class="form-elt">
                <label for="inventorynumber" class="me-2 text-end"><?= $Localizer['InventoryNumber']; ?></label>
                <input id="inventorynumber" type="text" name="inventorynumber" class="ts-form-control-light me-2"/>
            </div>
            <!-- Inventory date -->
            <div class="form-elt">
                <label for="inventorydate" class="me-2 text-end"><?= $Localizer['InventoryDate']; ?></label>
                <input id="inventorydate" type="datetime-local" name="inventorydate" value="<?= date('Y-m-d\TH:i'); ?>" class="ts-form-control-light me-2"/>
            </div>
            <!-- Warehouse ID -->
            <div class="form-elt">
                <label for="warehouseid" class="me-2 text-end"><?= $Localizer['WarehouseId']; ?></label>
                <select id="warehouseid" name="warehouseid" class="ts-form-control-light me-2">
                    <option disabled selected><?= $Localizer['WarehouseSelect'] ?></option>
                    <?php
                    if (isset($warehouses)) {
                        foreach ($warehouses as $warehouse) {
                            ?>
                            <option value="<?= $warehouse->It()->Id; ?>"><?= $warehouse->It()->Name; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Quantity -->
            <div class="form-elt">
                <label for="stockquantity" class="me-2 text-end"><?= $Localizer['StockQuantity']; ?></label>
                <input id="stockquantity" type="number" min="0" value="0" name="stockquantity" class="ts-form-control-light me-2"/>
            </div>
            <div id="stock-item" class="d-none"></div>
            <!-- Btn -->
            <div class="form-elt">
                <input type="hidden" name="state" value="false">
                <button class="btn btn-success"><?= $Localizer['InventoryAddBtn'] ?></button>
            </div>
        </div>
    </form>
    <!-- -->
    <form id="new-stockinventory" name="new-stockinventory" method="post" action="AddStockInventory">
       <!--Title-->
        <div class="title">
            <span><?= $components['NavNewInventory'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <div class="form-hd">
                <!-- Inventory number -->
                <div>
                    <label for="InventoryNumber"><?= $Localizer['InventoryNumber']; ?>: </label>
                    <span id="InventoryNumber" data-value=""></span>
                    <input type="hidden" name="inventorynumber" value="">
                </div>
                <!-- Inventory date -->
                <div>
                    <label for="InventoryDate"><?= $Localizer['InventoryDate']; ?>: </label>
                    <span id="InventoryDate" data-value=""></span>
                    <input type="hidden" name="inventorydate" value="">
                </div>
                <!-- Warehouse -->
                <div>
                    <label for="Warehouse"><?= $Localizer['WarehouseId']; ?>: </label>
                    <span id="Warehouse" data-value=""></span>
                    <input type="hidden" name="warehouseid" value="">
                </div>
                <!-- Credentials -->
                <div>
                    <label for="Credential"><?= $Localizer['CredentialId']; ?>: </label>
                    <span id="Credential" data-value=""><?= $_SESSION['LastName'] ?></span>
                    <input type="hidden" name="credentialid" value="<?= $_SESSION['ProfileId'] ?>">
                </div>
            </div>
            <!-- -->
            <div class="form-header">
                <div class="text-center"><?= $Localizer['HeaderNumber']; ?></div>
                <div><?= $Localizer['ProductId']; ?></div>
                <div><?= $Localizer['HeaderStock']; ?></div>
                <div><?= $Localizer['UnitId']; ?></div>
                <div class="text-end"><?= $Localizer['StockAvailable']; ?></div>
                <div class="text-end"><?= $Localizer['StockQuantity']; ?></div>
                <div class="text-end"><?= $Localizer['StockVariance']; ?></div>
                <div class="text-center"><span class="bi bi-trash-fill"></span></div>
            </div>
            <!-- -->
            <div class="form-area"></div>
            <!-- Dispatch description -->
            <div class="form-elt">
                <label for="inventorydesc" class="me-2 text-end"><?= $Localizer['InventoryDesc']; ?></label>
                <input id="inventorydesc" type="text" name="inventorydesc" class="ts-form-control-light me-2"/>
            </div>
            <!-- Btn -->
            <div class="form-elt">
                <button class="btn btn-success"><?= $Localizer['InventorySuccessBtn'] ?></button>
            </div>
        </div>
    </form>
</div>
