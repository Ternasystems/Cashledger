<?php
// View data
$components = $ViewData['components'];
$lang = $ViewData['CurrentLanguage'];
$products = $ViewData['products'];
$attributes = $ViewData['attributes'];
$units = $ViewData['units'];
$warehouses = $ViewData['warehouses'];
$manufacturers = $ViewData['manufacturers'];
$packagings = $ViewData['packagings'];
$suppliers = $ViewData['suppliers'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewStockDeliveryLocale.xml');

$locales = new Locales();

$Localizer = [
    'DeliveryNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'DeliveryNumber'),
    'Reference' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'Reference'),
    'DeliveryDate' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'DeliveryDate'),
    'ProductId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'ProductId'),
    'ProductSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'ProductSelect'),
    'ProductAttributes' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'ProductAttributes'),
    'AttributeCheck' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'AttributeCheck'),
    'AttributeSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'AttributeSelect'),
    'UnitId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'UnitId'),
    'UnitSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'UnitSelect'),
    'WarehouseId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'WarehouseId'),
    'WarehouseSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'WarehouseSelect'),
    'PackagingId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'PackagingId'),
    'PackagingSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'PackagingSelect'),
    'BatchNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'BatchNumber'),
    'StockQuantity' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'StockQuantity'),
    'UnitCost' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'UnitCost'),
    'SupplierId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'SupplierId'),
    'SupplierSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'SupplierSelect'),
    'HeaderNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'HeaderNumber'),
    'HeaderTotal' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'HeaderTotal'),
    'HeaderPackaging' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'HeaderPackaging'),
    'HeaderSupplier' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'HeaderSupplier'),
    'DeliveryDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'DeliveryDesc'),
    'DeliveryAddBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'DeliveryAddBtn'),
    'DeliverySuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDelivery', 'DeliverySuccessBtn')
];
?>
<div id="main">
    <form id="new-stock" name="new-stock" method="post" action="AddStock">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavStockDelivery'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Delivery number -->
            <div class="form-elt">
                <label for="deliverynumber" class="me-2 text-end"><?= $Localizer['DeliveryNumber']; ?></label>
                <input id="deliverynumber" type="text" name="deliverynumber" class="ts-form-control-light me-2"/>
            </div>
            <!-- Delivery reference -->
            <div class="form-elt">
                <label for="deliveryreference" class="me-2 text-end"><?= $Localizer['Reference']; ?></label>
                <input id="deliveryreference" type="text" name="deliveryreference" class="ts-form-control-light me-2"/>
            </div>
            <!-- Delivery date -->
            <div class="form-elt">
                <label for="deliverydate" class="me-2 text-end"><?= $Localizer['DeliveryDate']; ?></label>
                <input id="deliverydate" type="datetime-local" name="deliverydate" value="<?= date('Y-m-d\TH:i'); ?>" class="ts-form-control-light me-2"/>
            </div>
            <!-- Supplier ID -->
            <div class="form-elt">
                <label for="supplierid" class="me-2 text-end"><?= $Localizer['SupplierId']; ?></label>
                <select id="supplierid" name="supplierid" class="ts-form-control-light me-2">
                    <option disabled selected><?= $Localizer['SupplierSelect'] ?></option>
                    <?php
                    if (isset($suppliers)) {
                        foreach ($suppliers as $supplier) {
                            $fullname = null;
                            if (!empty($supplier->Profile()->FullName()['MaidenName'])){
                                $fullname = $supplier->Profile()->FullName()['MaidenName'];
                                if (!empty($supplier->Profile()->FullName()['FirstName']))
                                    $fullname .= ', '.$supplier->Profile()->FullName()['FirstName'];
                                $fullname .= ' '.$Localizer['Maiden'].' '.$supplier->Profile()->FullName()['LastName'];
                            }else{
                                $fullname = $supplier->Profile()->FullName()['LastName'];
                                if (!empty($supplier->Profile()->FullName()['FirstName']))
                                    $fullname .= ', '.$supplier->Profile()->FullName()['FirstName'];
                            }
                            ?>
                            <option value="<?= $supplier->It()->Id; ?>"><?= $fullname; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Batch number -->
            <div class="form-elt">
                <label for="batchnumber" class="me-2 text-end"><?= $Localizer['BatchNumber']; ?></label>
                <input id="batchnumber" type="text" name="batchnumber" class="ts-form-control-light me-2"/>
            </div>
            <!-- Products -->
            <div class="form-elt">
                <label for="productid" class="me-2 text-end"><?= $Localizer['ProductId']; ?></label>
                <select id="productid" name="productid" class="ts-form-control-light me-2">
                    <option disabled selected><?= $Localizer['ProductSelect'] ?></option>
                    <?php
                    if (isset($products)){
                        $relations = null;
                        foreach ($products as $product){
                            $relations[$product->It()->Id] = $product->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
                        }
                        asort($relations);
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>"><?= $relation; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Unit ID -->
            <div class="form-elt">
                <label for="unitid" class="me-2 text-end"><?= $Localizer['UnitId']; ?></label>
                <select id="unitid" name="unitid" class="ts-form-control-light me-2">
                    <option disabled selected><?= $Localizer['UnitSelect'] ?></option>
                    <?php
                    if (isset($units)){
                        $relations = null;
                        foreach ($units as $unit){
                            if ($unit->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId) == null) continue;
                            $relations[$unit->It()->Id] = $unit->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
                        }
                        asort($relations);
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>"><?= $relation; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
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
            <!-- Packaging ID -->
            <div class="form-elt">
                <label for="packagingid" class="me-2 text-end"><?= $Localizer['PackagingId']; ?></label>
                <select id="packagingid" name="packagingid" class="ts-form-control-light me-2">
                    <option disabled selected><?= $Localizer['PackagingSelect'] ?></option>
                    <?php
                    if (isset($packagings)){
                        $relations = null;
                        foreach ($packagings as $packaging){
                            if ($packaging->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId) == null) continue;
                            $relations[$packaging->It()->Id] = $packaging->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
                        }
                        asort($relations);
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>"><?= $relation; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Stock quantity -->
            <div class="form-elt">
                <label for="stockquantity" class="me-2 text-end"><?= $Localizer['StockQuantity']; ?></label>
                <input id="stockquantity" type="number" min="0" value="0" name="stockquantity" class="ts-form-control-light me-2"/>
            </div>
            <!-- Unit cost -->
            <div class="form-elt">
                <label for="unitcost" class="me-2 text-end"><?= $Localizer['UnitCost']; ?></label>
                <input id="unitcost" type="number" min="0" value="0" name="unitcost" class="ts-form-control-light me-2"/>
            </div>
            <!-- Attribute ID -->
            <div class="form-elt d-none">
                <label for="attributcheck" class="me-2 text-end"><?= $Localizer['AttributeCheck']; ?></label>
                <input id="attributcheck" type="checkbox" name="attributcheck" checked class="ts-form-control-light me-2">
            </div>
            <div class="form-elt d-none">
                <label for="attributes" class="me-2 text-end"><?= $Localizer['ProductAttributes']; ?></label>
                <select id="attributes" name="attributes" multiple size="5" class="ts-form-control-light me-2 ts-disabled">
                    <option value="0" disabled selected><?= $Localizer['AttributeSelect'] ?></option>
                    <?php
                    if (isset($attributes)){
                        $relations = null;
                        foreach ($attributes as $attribute){
                            if ($attribute->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId) == null) continue;
                            $relations[$attribute->It()->Id] = [
                                'AttributeType' => $attribute->It()->AttributeType,
                                'AttributeTable' => $attribute->It()->AttributeTable,
                                'Label' => $attribute->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label
                            ];
                        }
                        uasort($relations, function ($a, $b){
                            return strcmp($a['Label'], $b['Label']);
                        });
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>" data-type="<?= $relation['AttributeType'] ?>" data-table="<?= $relation['AttributeTable'] ?>">
                                <?= $relation['Label']; ?>
                            </option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Btn -->
            <div class="form-elt">
                <input type="hidden" name="state" value="false">
                <button class="btn btn-success"><?= $Localizer['DeliveryAddBtn'] ?></button>
            </div>
        </div>
    </form>
    <!-- -->
    <form id="new-stockdelivery" name="new-stockdelivery" method="post" action="AddStockDelivery">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavStockDelivery'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <div class="form-hd">
                <!-- Delivery number -->
                <div>
                    <label for="DeliveryNumber"><?= $Localizer['DeliveryNumber']; ?>: </label>
                    <span id="DeliveryNumber" data-value=""></span>
                    <input type="hidden" name="deliverynumber" value="">
                </div>
                <!-- Delivery date -->
                <div>
                    <label for="DeliveryDate"><?= $Localizer['DeliveryDate']; ?>: </label>
                    <span id="DeliveryDate" data-value=""></span>
                    <input type="hidden" name="deliverydate" value="">
                </div>
                <!-- Delivery reference -->
                <div>
                    <label for="Reference"><?= $Localizer['Reference']; ?>: </label>
                    <span id="Reference" data-value=""></span>
                    <input type="hidden" name="deliveryreference" value="">
                </div>
                <!-- Delivery supplier -->
                <div>
                    <label for="HeaderSupplier"><?= $Localizer['HeaderSupplier']; ?>: </label>
                    <span id="HeaderSupplier" data-value=""></span>
                    <input type="hidden" name="supplierid" value="">
                </div>
            </div>
            <!-- -->
            <div class="form-header">
                <div class="text-center"><?= $Localizer['HeaderNumber']; ?></div>
                <div><?= $Localizer['ProductId']; ?></div>
                <div><?= $Localizer['BatchNumber']; ?></div>
                <div><?= $Localizer['WarehouseId']; ?></div>
                <div><?= $Localizer['HeaderPackaging']; ?></div>
                <div><?= $Localizer['UnitId']; ?></div>
                <div class="text-end"><?= $Localizer['StockQuantity']; ?></div>
                <div class="text-end"><?= $Localizer['UnitCost']; ?></div>
                <div class="text-end"><?= $Localizer['HeaderTotal']; ?></div>
                <div class="text-center"><span class="bi bi-trash-fill"></span></div>
            </div>
            <!-- -->
            <div class="form-area">
                <div id="totalcost" class="text-end">
                    <label class="fw-bold"><?= $Localizer['HeaderTotal']; ?> = </label>
                    <span></span>
                </div>
            </div>
            <!-- Delivery description -->
            <div class="form-elt">
                <label for="deliverydesc" class="me-2 text-end"><?= $Localizer['DeliveryDesc']; ?></label>
                <input id="deliverydesc" type="text" name="deliverydesc" class="ts-form-control-light me-2"/>
            </div>
            <!-- Btn -->
            <div class="form-elt">
                <button class="btn btn-success"><?= $Localizer['DeliverySuccessBtn'] ?></button>
            </div>
        </div>
    </form>
</div>
