<!-- Presentation Dashboard page -->
<main>
    <?php
    $lang = $ViewData['CurrentLanguage'];
    $apps = $ViewData['apps'];
    $prop = 'title';
    //
    uasort($apps, function($a, $b) use ($lang, $prop) {
        $elt1 = $a[$lang][$prop] ?? '';
        $elt2 = $b[$lang][$prop] ?? '';
        return strcasecmp($elt1, $elt2);
    });
    //
    foreach ($apps as $app) {
        $title = $app[$lang]['title'];
        $logo = $app[$lang]['logo'];
        $logo = $ViewData[$logo];
        $description = $app[$lang]['description'];
        $dapp = $app['url']['app'];
        $ctrl = $app['url']['controller'];
        $action = $app['url']['action'];
        ?>
        <section class="ts-app">
            <img src="<?= $logo ?>">
            <div>
                <span class="ts-app-title fw-bold"><?= $title; ?></span>
                <span class="ts-app-desc"><?= $description; ?></span>
                <button class="btn ts-link" data-app="<?= $dapp; ?>" data-controller="<?= $ctrl; ?>" data-action="<?= $action; ?>"><?= $Localizer['Launch'] ?></button>
            </div>
        </section>
        <?php
    }
    ?>
</main>
