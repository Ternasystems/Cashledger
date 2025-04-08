<?php
// View data
$components = $ViewData['components'];
$lang = $ViewData['CurrentLanguage'];
$warehouses = $ViewData['warehouses'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewWarehouseLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'WarehouseName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Warehouse', 'WarehouseName'),
    'WarehouseLocation' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Warehouse', 'WarehouseLocation'),
    'WarehouseDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Warehouse', 'WarehouseDesc'),
    'WarehouseSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Warehouse', 'WarehouseSuccessBtn'),
    'WarehouseList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Warehouse', 'WarehouseList')
];
?>
<div id="main">
    <form id="new-warehouse" name="new-warehouse" method="post" action="AddWarehouse">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavNewWarehouse'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Warehouse name -->
            <div class="form-elt">
                <label for="warehousename" class="me-2 text-end"><?= $Localizer['WarehouseName']; ?></label>
                <input id="warehousename" type="text" name="warehousename" class="ts-form-control-light me-2"/>
            </div>
            <!-- Warehouse location -->
            <div class="form-elt">
                <label for="warehouselocation" class="me-2 text-end"><?= $Localizer['WarehouseLocation']; ?></label>
                <input id="warehouselocation" type="text" name="warehouselocation" class="ts-form-control-light me-2"/>
            </div>
            <!-- Warehouse description -->
            <div class="form-elt">
                <label for="warehousedesc" class="me-2 text-end"><?= $Localizer['WarehouseDesc']; ?></label>
                <input id="warehousedesc" type="text" name="warehousedesc" class="ts-form-control-light me-2"/>
            </div>
            <!-- Warehouse name -->
            <div class="form-elt">
                <button class="btn btn-success"><?= $Localizer['WarehouseSuccessBtn'] ?></button>
            </div>
        </div>
    </form>
    <!-- -->
    <div id="warehouse-list">
        <div class="title">
            <span><?= $Localizer['WarehouseList']; ?></span>
        </div>
        <?php
        if (isset($warehouses)) {
            foreach ($warehouses as $warehouse) {
                ?>
                <div class="warehouse-elt"><?= $warehouse->It()->Name; ?></div>
                <?php
            }
        }
        ?>
    </div>
</div>