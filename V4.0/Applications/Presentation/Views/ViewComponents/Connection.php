<?php
/* Presentation Connection form page */

// Read locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\HomeLocale.xml');

// Instantiate Locales
$locales = new Locales();

// Languages
$languages = $ViewData['languages'];
$appVersion = $ViewData['appVersion'];
$ip = $ViewData['IP'];

// Localizer
$Localizer = [
    'Title' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Home', 'Title'),
    'ChooseUser' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Home', 'ChooseUser'),
    'Username' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Home', 'Username'),
    'UsernameValidation' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Home', 'UsernameValidation'),
    'Pwd' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Home', 'Pwd'),
    'PwdValidation' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Home', 'PwdValidation'),
    'ForgottenPwd' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Home', 'ForgottenPwd'),
    'Login' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Presentation', 'Home', 'Login')
];
?>

<main class="container-fluid">
    <form id="connection" name="connection" method="post" action="Connection" class="col-lg-2">
        <!--Title-->
        <div id="title">
            <picture>
                <source media="(min-width: 1024px)" srcset="<?= $ViewData['Logo']; ?>">
                <img src="<?= $ViewData['Logo_mobile']; ?>">
            </picture>
        </div>
        <div id="form-body">
            <!-- Username -->
            <div class="form-elt">
                <a id="choose-user" class="ts-view" data-app="Presentation" data-parent="form-body" data-component="ChooseUser"><?= $Localizer['ChooseUser'] ?></a>
                <input id="username" type="text" name="username" value="<?= $ViewData['UserName'] ?? '' ?>" placeholder="<?= $Localizer['Username']; ?>" class="ts-form-control"/>
                <span id="username-validation" class="text-danger d-none"><?= $Localizer['UsernameValidation']; ?></span>
            </div>
            <!-- Password -->
            <div class="form-elt">
                <a id="forgotten-pwd" class="ts-link" data-app="Presentation" data-controller="Home" data-action="ResetPassword"><?= $Localizer['ForgottenPwd'] ?></a>
                <input id="pwd" type="password" name="pwd" placeholder="<?= $Localizer['Pwd']; ?>" class="ts-form-control"/>
                <span id="pwd-validation" class="text-danger d-none"><?= $Localizer['PwdValidation']; ?></span>
            </div>
            <!-- IP -->
            <div class="form-elt">
                <input id="ip" type="hidden" name="ip" value="<?= $ip; ?>" class="ts-form-control">
            </div>
            <!-- Buttons -->
            <div class="form-elt">
                <button class="btn btn-success"><?= $Localizer['Login'] ?></button>
            </div>
            <!-- Language -->
            <div class="form-elt">
                <select id="lang" data-app="Presentation" data-controller="Home" data-action="Index" class="ts-form-control-light">
                    <?php
                    foreach ($languages as $key => $language) {
                        ?>
                        <option value="<?= $language->LanguageRelations()[0]->Label; ?>" data-default="<?= $ViewData['CurrentLanguage'] ?>"><?= ucfirst($language->It()->Description) ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div id="manifest"><?= $appVersion; ?></div>
    </form>
</main>