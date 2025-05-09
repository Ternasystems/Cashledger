<?php
$lang = $ViewData['CurrentLanguage'];
$stockNumber = $ViewData['stockNumber'];
$model = $ViewData['model'];
$product = $ViewData['product'];
$unit = $ViewData['unit'];
$warehouse = $ViewData['warehouse'];
$packaging = $ViewData['packaging'];
$customer = $ViewData['customer'];
$languages = $ViewData['languages'];

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewStockDispatchLocale.xml');

$locales = new Locales();

$Localizer = [
    'Maiden' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'StockDispatch', 'Maiden')
];

$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
$numberFormatter = new NumberFormatter($langId, NumberFormatter::DECIMAL);
$json = json_decode($model->json);
//
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
<div class="form-row">
    <div class="text-center"><?= sprintf("%03d", $stockNumber); ?></div>
    <div><?= $product->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label; ?></div>
    <div><?= $json->BatchNumber; ?></div>
    <div><?= $warehouse->It()->Name; ?></div>
    <div><?= $packaging->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label; ?></div>
    <div><?= $unit->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label; ?></div>
    <div><?= $fullname; ?></div>
    <div data-id="total" class="text-end"><?= $numberFormatter->format($model->stockquantity); ?></div>
    <div class="text-center"><span class="bi bi-trash-fill"></span></div>
    <input type="hidden" name="StockModel[<?= $stockNumber; ?>]" value="<?= htmlspecialchars(json_encode($model), ENT_QUOTES); ?>">
</div>