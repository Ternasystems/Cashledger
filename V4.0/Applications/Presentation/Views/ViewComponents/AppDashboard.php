<!-- Presentation Dashboard page -->
<main>
    <?php
    $lang = $ViewData['CurrentLanguage'];
    $apps = $ViewData['apps'];
    $registeredApps = $ViewData['registeredApps'];
    $prop = 'title';
    //
    uasort($apps, function($a, $b) use ($lang, $prop) {
        $elt1 = $a[$lang][$prop] ?? '';
        $elt2 = $b[$lang][$prop] ?? '';
        return strcasecmp($elt1, $elt2);
    });

    // Registered apps
    ?>
    <div class="section-line mt-3">
        <span><?= $Localizer['RegisteredSection']; ?></span>
    </div>
    <?php
    foreach ($apps as $key => $app) {
        if (!$registeredApps->Any(fn($n) => $n->It()->Name == $key)) continue;
        $name = $registeredApps->FirstOrDefault(fn($n) => $n->It()->Name == $key)->It()->Name;
        $title = $apps[$name][$lang]['title'];
        $logo = $apps[$name][$lang]['logo'];
        $logo = $ViewData[$logo];
        $description = $apps[$name][$lang]['description'];
        $dapp = $apps[$name]['url']['app'];
        $ctrl = $apps[$name]['url']['controller'];
        $action = $apps[$name]['url']['action'];
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

    //
    ?>
    <div class="section-line mt-5">
        <span><?= $Localizer['UnregisteredSection']; ?></span>
    </div>
    <?php
    foreach ($apps as $key => $app) {
        if ($registeredApps->Any(fn($n) => $n->It()->Name == $key)) continue;
        $title = $app[$lang]['title'];
        $logo = $app[$lang]['logo'];
        $logo = $ViewData[$logo];
        $description = $app[$lang]['description'];
        $dapp = $app['url']['app'];
        $ctrl = $app['url']['controller'];
        $action = $app['url']['action'];
        ?>
        <section class="ts-app ts-disabled">
            <img src="<?= $logo ?>">
            <div>
                <span class="ts-app-title fw-bold"><?= $title; ?></span>
                <span class="ts-app-desc"><?= $description; ?></span>
            </div>
        </section>
        <?php
    }
    ?>
</main>
