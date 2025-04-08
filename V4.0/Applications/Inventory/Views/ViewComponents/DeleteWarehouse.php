<?php
// View data
$components = $ViewData["components"];
$lang = $ViewData['CurrentLanguage'];
$warehouses = $ViewData['warehouses'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\ModifyWarehouseLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'WarehouseName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Warehouse', 'WarehouseName'),
    'WarehouseDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Warehouse', 'WarehouseDesc'),
    'WarehouseSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Warehouse', 'WarehouseSuccessBtn'),
    'WarehouseList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Warehouse', 'WarehouseList')
];
?>
<div id="main">
    <div id="warehouse-delete-list">
        <div class="title">
            <span><?= $Localizer['WarehouseList']; ?></span>
        </div>
        <?php
        if (isset($warehouses)) {
            foreach ($warehouses->SortBy(fn($n) => $n->It()->Name) as $warehouse) {
                ?>
                <div class="warehouse-elt ts-elt d-flex justify-content-between" data-id="<?= $warehouse->It()->Id; ?>">
                    <span><?= $warehouse->It()->Name; ?></span>
                    <span class="bi bi-trash"></span>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>