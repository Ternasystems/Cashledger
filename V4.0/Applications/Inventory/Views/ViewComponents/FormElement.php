<?php
$lang = $ViewData['CurrentLanguage'];
$attrType = $ViewData['attrType'];
$attrTable = $ViewData['attrTable'];
$collection = $ViewData['collection'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
?>
<div class="form-elt">
    <label for="" class="me-2 text-end"></label>
    <?php
    switch ($attrType) {
        case 'timestamp':
        {
            ?>
            <input id="" type="date" name="" data-class="formelement" value="<?= date('Y-m-d') ?>" class="ts-form-control-light me-2"/>
            <?php
        }
        case 'number':
        {
            ?>
            <input id="" type="number" name="" data-class="formelement" value="0" class="ts-form-control-light me-2"/>
            <?php
        }
        break;
        case 'table':
        {
            $relations = null;
            if (!is_null($collection[0]->LanguageRelations())) {
                foreach ($collection as $item) {
                    if ($item->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId) == null) continue;
                    $relations[$item->It()->Id] = $item->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label;
                }
                asort($relations);
            }else{
                if (!method_exists($collection[0], 'Profile')){
                    foreach ($collection as $item)
                        $relations[$item->It()->Id] = $item->It()->Name;
                }else{
                    foreach ($collection as $item)
                        $relations[$item->It()->Id] = $item->Profile()->FullName()['LastName'];
                }
            }
            ?>
            <select id="" name="" data-class="formelement" class="ts-form-control-light me-2">
                <?php
                foreach ($relations as $key => $relation) {
                    ?>
                    <option value="<?= $key ?>"><?= $relation ?></option>
                    <?php
                }
                ?>
            </select>
            <?php
        }
        break;
        case 'text':
        default:
        {
            ?>
            <input id="" type="text" name="" data-class="formelement" class="ts-form-control-light me-2"/>
            <?php
        }
    }
    ?>
</div>