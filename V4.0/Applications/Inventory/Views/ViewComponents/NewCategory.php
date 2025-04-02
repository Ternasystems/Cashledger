<?php
// View data
$components = $ViewData["components"];
$lang = $ViewData['CurrentLanguage'];
?>
<form id="new-category" name="new-category" method="post" action="NewCategory">
    <!--Title-->
    <div id="title">
        <span><?= $components['NavNewProductCategory'][$lang]['title']; ?></span>
    </div>
    <div id="form-body">
    </div>
</form>