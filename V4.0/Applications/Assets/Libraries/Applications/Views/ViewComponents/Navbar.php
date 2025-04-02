<nav>
    <div class="container-fluid d-flex justify-content-between align-items-center py-2">
        <div id="nav-1" class="d-flex align-items-center">
            <div id="nav-title"><?= $Localizer['NavTitle'] ?></div>
            <?php
            $components = $ViewData['components'];
            foreach ($NavItems as $NavItem) {
                $dapp = $components[$NavItem]['url']['app'];
                $parent = $components[$NavItem]['url']['parent'];
                $component = $components[$NavItem]['url']['component'];
                ?>
                <div class="ts-view" data-app="<?= $dapp; ?>" data-parent="<?= $parent; ?>" data-component="<?= $component; ?>"><?= $Localizer[$NavItem] ?></div>
                <?php
            }
            ?>
        </div>
        <div id="nav-2">
            <div id="nav-page"></div>
            <div id="nav-btn">
                <span class="bi bi-chevron-left btn btn-outline-secondary ts-disabled"></span>
                <span class="bi bi-chevron-right btn btn-outline-secondary ts-disabled"></span>
            </div>
        </div>
    </div>
</nav>