<?php
// View data
$components = $ViewData["components"];
$lang = $ViewData['CurrentLanguage'];
?>
<form id="modify-category" name="modify-category" method="post" action="ModifyCategory">
    <!--Title-->
    <div id="title">
        <span><?= $components['NavModifyProductCategory'][$lang]['title']; ?></span>
    </div>
    <div id="form-body">
    </div>
</form>