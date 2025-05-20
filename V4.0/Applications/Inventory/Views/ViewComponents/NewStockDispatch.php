<?php
// View data
$components = $ViewData['components'];
$lang = $ViewData['CurrentLanguage'];
$products = $ViewData['products'];
$units = $ViewData['units'];
$warehouses = $ViewData['warehouses'];
$manufacturers = $ViewData['manufacturers'];
$packagings = $ViewData['packagings'];
$suppliers = $ViewData['suppliers'];
$customers = $ViewData['customers'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewStockDispatchLocale.xml');

$locales = new Locales();

$Localizer = [
    'DispatchNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'DispatchNumber'),
    'Reference' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'Reference'),
    'DispatchDate' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'DispatchDate'),
    'CustomerId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'CustomerId'),
    'CustomerSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'CustomerSelect'),
    'ProductId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'ProductId'),
    'ProductSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'ProductSelect'),
    'StockQuantity' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'StockQuantity'),
    'HeaderNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'HeaderNumber'),
    'HeaderTotal' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'HeaderTotal'),
    'HeaderPackaging' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'HeaderPackaging'),
    'HeaderSupplier' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'HeaderSupplier'),
    'DispatchDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'DispatchDesc'),
    'DispatchAddBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'DispatchAddBtn'),
    'BatchNumber' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'BatchNumber'),
    'UnitId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'UnitId'),
    'WarehouseId' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'WarehouseId'),
    'DispatchSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'DispatchSuccessBtn')
];
?>
<div id="main">
    <form id="modify-stock" name="modify-stock" method="post" action="RemoveStock">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavStockDispatch'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Dispatch number -->
            <div class="form-elt">
                <label for="dispatchnumber" class="me-2 text-end"><?= $Localizer['DispatchNumber']; ?></label>
                <input id="dispatchnumber" type="text" name="dispatchnumber" class="ts-form-control-light me-2"/>
            </div>
            <!-- Dispatch reference -->
            <div class="form-elt">
                <label for="dispatchreference" class="me-2 text-end"><?= $Localizer['Reference']; ?></label>
                <input id="dispatchreference" type="text" name="dispatchreference" class="ts-form-control-light me-2"/>
            </div>
            <!-- Dispatch date -->
            <div class="form-elt">
                <label for="dispatchdate" class="me-2 text-end"><?= $Localizer['DispatchDate']; ?></label>
                <input id="dispatchdate" type="datetime-local" name="dispatchdate" value="<?= date('Y-m-d\TH:i'); ?>" class="ts-form-control-light me-2"/>
            </div>
            <!-- Customer ID -->
            <div class="form-elt">
                <label for="customerid" class="me-2 text-end"><?= $Localizer['CustomerId']; ?></label>
                <select id="customerid" name="customerid" class="ts-form-control-light me-2">
                    <option disabled selected><?= $Localizer['CustomerSelect'] ?></option>
                    <?php
                    if (isset($customers)) {
                        foreach ($customers as $customer) {
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
                            <option value="<?= $customer->It()->Id; ?>"><?= $fullname; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
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
            <!-- Quantity -->
            <div class="form-elt">
                <label for="stockquantity" class="me-2 text-end"><?= $Localizer['StockQuantity']; ?></label>
                <input id="stockquantity" type="number" min="0" value="0" name="stockquantity" class="ts-form-control-light me-2"/>
            </div>
            <div id="stock-item" class="d-none"></div>
            <!-- Btn -->
            <div class="form-elt">
                <input type="hidden" name="state" value="false">
                <button class="btn btn-success"><?= $Localizer['DispatchAddBtn'] ?></button>
            </div>
        </div>
    </form>
    <!-- -->
    <form id="new-stockdispatch" name="new-stockdispatch" method="post" action="AddStockDispatch">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavStockDispatch'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <div class="form-hd">
                <!-- Dispatch number -->
                <div>
                    <label for="DispatchNumber"><?= $Localizer['DispatchNumber']; ?>: </label>
                    <span id="DispatchNumber" data-value=""></span>
                    <input type="hidden" name="dispatchnumber" value="">
                </div>
                <!-- Dispatch date -->
                <div>
                    <label for="DispatchDate"><?= $Localizer['DispatchDate']; ?>: </label>
                    <span id="DispatchDate" data-value=""></span>
                    <input type="hidden" name="dispatchdate" value="">
                </div>
                <!-- Dispatch reference -->
                <div>
                    <label for="Reference"><?= $Localizer['Reference']; ?>: </label>
                    <span id="Reference" data-value=""></span>
                    <input type="hidden" name="dispatchreference" value="">
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
                <div><?= $Localizer['CustomerId']; ?></div>
                <div class="text-end"><?= $Localizer['StockQuantity']; ?></div>
                <div class="text-center"><span class="bi bi-trash-fill"></span></div>
            </div>
            <!-- -->
            <div class="form-area">
                <div id="totalcost" class="text-end">
                    <label class="fw-bold"><?= $Localizer['HeaderTotal']; ?> = </label>
                    <span data-total="0"></span>
                </div>
            </div>
            <!-- Dispatch description -->
            <div class="form-elt">
                <label for="dispatchdesc" class="me-2 text-end"><?= $Localizer['DispatchDesc']; ?></label>
                <input id="dispatchdesc" type="text" name="dispatchdesc" class="ts-form-control-light me-2"/>
            </div>
            <!-- Btn -->
            <div class="form-elt">
                <button class="btn btn-success"><?= $Localizer['DispatchSuccessBtn'] ?></button>
            </div>
        </div>
    </form>
</div>
