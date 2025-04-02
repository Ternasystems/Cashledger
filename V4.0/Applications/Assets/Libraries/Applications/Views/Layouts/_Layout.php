<!--Presentation Home Layout -->

<!DOCTYPE html>
<html lang="<?= $ViewData['DefaultLanguage']; ?>">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $Localizer['Title']; ?></title>
    <link rel="shortcut icon" href="<?= $ViewData['Icon']; ?>"/>
    <link rel="stylesheet" href="<?= $ViewData['Bootstrap']; ?>"/>
    <link rel="stylesheet" href="<?= $ViewData['BootstrapIcon']; ?>"/>
    <link rel="stylesheet" href="<?= $ViewData['css']; ?>"/>
    <script src="<?= $ViewData['JQuery']; ?>"></script>
    <script src="<?= $ViewData['AjaxUnobstrusive']; ?>"></script>
    <script src="<?= $ViewData['RxJS']; ?>"></script>
    <script src="<?= $ViewData['CryptoJS']; ?>"></script>
    <script src="<?= $ViewData['GlobalJS']; ?>"></script>
    <script src="<?= $ViewData['js']; ?>"></script>
</head>

<body>
<?= $content; ?>
</body>
</html>