<?php
$apps = $ViewData['apps'];

foreach ($MenuItems as $key => $MenuItem) {
    ?>
    <div id="HomeMenu" class="ts-menu p-3">
        <span><?= $Localizer[$key] ?></span>
        <!-- Menu -->
        <ul class="ts-list d-none"><?php
            foreach ($MenuItem as $Item) {
                $dapp = $apps[$Item]['url']['app'];
                $ctrl = $apps[$Item]['url']['controller'];
                $action = $apps[$Item]['url']['action'];
                ?>
                <li class="ts-link" data-app="<?= $dapp; ?>" data-controller="<?= $ctrl; ?>" data-action="<?= $action; ?>"><?= $Localizer[$Item] ?></li>
                <?php
            }
            ?>
        </ul>
    </div>
    <?php
}