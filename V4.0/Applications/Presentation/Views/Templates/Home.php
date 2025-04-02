<?php
/* Presentation Home template */

// Set statics
$ViewData['css'] = $ViewData['cssPath'].'css.Presentation/Index.css';
$ViewData['js'] = $ViewData['jsPath'].'js.Presentation/Presentation.js';

// Start buffering
ob_start();

// Insert connection form
include dirname(__DIR__, 1).'/ViewComponents/Connection.php';

$content = ob_get_clean();

// Include layout
require $ViewData['layoutPath'].'_Layout.php';