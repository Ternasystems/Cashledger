<nav data-usr="<?= $_SESSION['LastName']; ?>">
    <div class="container-fluid d-flex justify-content-between align-items-center py-2">
        <div id="nav-1" class="d-flex align-items-center">
            <div id="nav-title"><?= $Localizer['NavTitle'] ?></div>
        </div>
        <div id="nav-2" class="d-flex">
            <div id="nav-display">
                <span id="display-grid" class="bi bi-grid-3x3-gap-fill"></span>
                <span id="display-list" class="bi bi-list"></span>
                <span id="display-task" class="bi bi-list-task"></span>
            </div>
            <div id="nav-sort">
                <span id="sort-up" class="bi bi-sort-alpha-up"></span>
                <span id="sort-down" class="bi bi-sort-alpha-down"></span>
            </div>
            <div id="nav-group">
                <span id="group-none" class="bi bi-view-stacked"></span>
                <span id="group-cat" class="bi bi-view-list"></span>
            </div>
        </div>
    </div>
</nav>