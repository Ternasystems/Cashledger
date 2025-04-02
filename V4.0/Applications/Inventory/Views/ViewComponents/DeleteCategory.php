<?php
// View data
$components = $ViewData["components"];
$lang = $ViewData['CurrentLanguage'];
?>
<form id="delete-category" name="delete-category" method="post" action="DeleteCategory">
    <!--Title-->
    <div id="title">
        <span><?= $components['NavDeleteProductCategory'][$lang]['title']; ?></span>
    </div>
    <div id="form-body">
    </div>
</form>