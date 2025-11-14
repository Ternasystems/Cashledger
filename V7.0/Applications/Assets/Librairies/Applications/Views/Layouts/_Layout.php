<!--Shared Layout -->

<!DOCTYPE html>
<html lang="<?= $this->h($lang); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?= $this->h($images['Icon'] ?? ''); ?>"/>
    <link rel="stylesheet" href="<?= $this->h($paths['c2s'] ?? '') ?>"/>
    <link rel="stylesheet" href="<?= $this->h($paths['faBrands'] ?? '') ?>"/>
    <link rel="stylesheet" href="<?= $this->h($paths['faRegular'] ?? '') ?>"/>
    <link rel="stylesheet" href="<?= $this->h($paths['faSolid'] ?? '') ?>"/>
    <link rel="stylesheet" href="<?= $this->h($css) ?>"/>
    <title><?= $this->section('Title', 'Cashledger'); ?></title>
</head>

<body>
    <!-- This 'section' renders the content of the child view -->
    <?= $this->section('content'); ?>

    <!-- Core Framework JS -->
    <script src="<?= $this->h($paths['cjs']); ?> "></script>
    <script src="<?= $this->h($js); ?> "></script>
</body>
</html>