<?php
$lang = $ViewData['CurrentLanguage'];
$stockNumber = $ViewData['stockNumber'];
$model = $ViewData['model'];
$product = $ViewData['product'];
$unit = $ViewData['unit'];
$warehouse = $ViewData['warehouse'];
$packaging = $ViewData['packaging'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
$numberFormatter = new NumberFormatter($langId, NumberFormatter::DECIMAL);
$currencyFormatter = new NumberFormatter('fr-CM', NumberFormatter::CURRENCY);
?>
<div class="form-row">
    <div class="text-center"><?= sprintf("%03d", $stockNumber); ?></div>
    <div><?= $product->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label; ?></div>
    <div><?= $model->batchnumber; ?></div>
    <div><?= $warehouse->It()->Name; ?></div>
    <div><?= $packaging->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label; ?></div>
    <div><?= $unit->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label; ?></div>
    <div class="text-end"><?= $numberFormatter->format($model->stockquantity); ?></div>
    <div class="text-end"><?= $currencyFormatter->format($model->unitcost); ?></div>
    <div data-id="total" class="text-end"><?= $currencyFormatter->format($model->stockquantity * $model->unitcost); ?></div>
    <div class="text-center"><span class="bi bi-trash-fill"></span></div>
    <input type="hidden" name="StockModel[<?= $stockNumber; ?>]" value="<?= htmlspecialchars(json_encode($model), ENT_QUOTES); ?>">
</div>