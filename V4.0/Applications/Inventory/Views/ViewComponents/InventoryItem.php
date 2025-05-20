<?php
$lang = $ViewData['CurrentLanguage'];
$stockNumber = $ViewData['stockNumber'];
$model = $ViewData['model'];
$product = $ViewData['product'];
$unit = $ViewData['unit'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
$numberFormatter = new NumberFormatter($langId, NumberFormatter::DECIMAL);
$json = json_decode($model->json);
?>
<div class="form-row">
    <div class="text-center"><?= sprintf("%03d", $stockNumber); ?></div>
    <div><?= $product->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label; ?></div>
    <div data-id="stock-id"><?= $model->stockid; ?></div>
    <div><?= $unit->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label; ?></div>
    <div data-id="stock-available" class="text-end"><?= $numberFormatter->format($model->stockavailable); ?></div>
    <div data-id="stock-quantity" class="text-end"><?= $numberFormatter->format($model->stockquantity); ?></div>
    <div data-id="variation" class="text-end"><?= $numberFormatter->format($model->stockavailable - $model->stockquantity); ?></div>
    <div class="text-center"><span class="bi bi-trash-fill"></span></div>
    <input type="hidden" name="StockModel[<?= $stockNumber; ?>]" value="<?= htmlspecialchars(json_encode($model), ENT_QUOTES); ?>">
</div>
