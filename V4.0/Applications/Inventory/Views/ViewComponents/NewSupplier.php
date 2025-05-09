<?php
// View data
$components = $ViewData['components'];
$lang = $ViewData['CurrentLanguage'];
$suppliers = $ViewData['suppliers'];
$civilities = $ViewData['civilities'];
$contactTypes = $ViewData['contactTypes'];
$countries = $ViewData['countries'];
$cities = $ViewData['cities'];
$languages = $ViewData['languages'];
$langId = $languages->FirstOrDefault(fn($n) => str_contains($lang, $n->It()->Label))->It()->Id;
//
$phoneId = $contactTypes->Where(fn($n) => $n->It()->Name == 'Phone')->FirstOrDefault()->It()->Id;
$phone = $contactTypes->Where(fn($n) => $n->It()->Name == 'Phone')->FirstOrDefault()->It()->Name;
$phonePhoto = basename($ViewData['Social_Phone']);
//
$emailId = $contactTypes->Where(fn($n) => $n->It()->Name == 'Email')->FirstOrDefault()->It()->Id;
$email = $contactTypes->Where(fn($n) => $n->It()->Name == 'Email')->FirstOrDefault()->It()->Name;
$emailPhoto = basename($ViewData['Social_Email']);
//
$locationId = $contactTypes->Where(fn($n) => $n->It()->Name == 'Address')->FirstOrDefault()->It()->Id;
$location = $contactTypes->Where(fn($n) => $n->It()->Name == 'Address')->FirstOrDefault()->It()->Name;
$locationPhoto = basename($ViewData['Social_Location']);

// Locales
use TS_Configuration\Classes\XMLManager;
use TS_Locale\Classes\Locales;

$xmlLocale = new XMLManager(dirname(__DIR__, 2).'\Assets\Locales\NewSupplierLocale.xml');

// Instantiate Locales
$locales = new Locales();

$Localizer = [
    'FirstName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'FirstName'),
    'LastName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'LastName'),
    'MaidenName' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'MaidenName'),
    'Maiden' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Maiden'),
    'Birthdate' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Birthdate'),
    'Civility' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Civility'),
    'Gender' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Gender'),
    'Occupation' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Occupation'),
    'Status' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Status'),
    'Title' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Title'),
    'Country' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Country'),
    'City' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'City'),
    'CivilitySelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'CivilitySelect'),
    'GenderSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'GenderSelect'),
    'OccupationSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'OccupationSelect'),
    'StatusSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'StatusSelect'),
    'TitleSelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'TitleSelect'),
    'CountrySelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'CountrySelect'),
    'CitySelect' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'CitySelect'),
    'Phone1' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Phone1'),
    'Phone2' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Phone2'),
    'Location' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Location'),
    'Email' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'Email'),
    'SupplierDesc' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'SupplierDesc'),
    'SupplierSuccessBtn' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'SupplierSuccessBtn'),
    'SupplierList' => $locales->getLocale($xmlLocale, $ViewData['CurrentLanguage'], 'Inventory', 'Supplier', 'SupplierList')
];
?>
<div id="main">
    <form id="new-supplier" name="new-supplier" method="post" action="AddSupplier">
        <!--Title-->
        <div class="title">
            <span><?= $components['NavNewSupplier'][$lang]['title']; ?></span>
        </div>
        <div id="form-body">
            <!-- Supplier civility -->
            <div class="form-elt">
                <label for="civilities" class="me-2 text-end"><?= $Localizer['Civility']; ?></label>
                <select id="civilities" name="civilities[CivilityId]" class="ts-form-control-light me-2">
                    <option data-value="" disabled selected><?= $Localizer['CivilitySelect'] ?></option>
                    <?php
                    if (isset($civilities['Civilities'])) {
                        $_civilities = $civilities['Civilities'];
                        $relations = null;
                        foreach ($_civilities as $_civility) {
                            $relations[$_civility->It()->Id] = [
                                'Label' => $_civility->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label,
                                'Name' => $_civility->It()->Name
                            ];
                        }
                        uasort($relations, function($a, $b) {
                            if ($a['Name'] === 'Non applicable') return -1;
                            if ($b['Name'] === 'Non applicable') return 1;
                            return strcmp($a['Label'], $b['Label']);
                        });
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>" data-value="<?= $relation['Name']; ?>"><?= $relation['Label']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Supplier firstname -->
            <div class="form-elt">
                <label for="firstname" class="me-2 text-end"><?= $Localizer['FirstName']; ?></label>
                <input id="firstname" type="text" name="firstname" class="ts-form-control-light me-2"/>
            </div>
            <!-- Supplier maidenname -->
            <div class="form-elt">
                <label for="maidenname" class="me-2 text-end"><?= $Localizer['MaidenName']; ?></label>
                <input id="maidenname" type="text" name="maidenname" class="ts-form-control-light me-2"/>
            </div>
            <!-- Supplier lastname -->
            <div class="form-elt">
                <label for="lastname" class="me-2 text-end"><?= $Localizer['LastName']; ?></label>
                <input id="lastname" type="text" name="lastname" class="ts-form-control-light me-2"/>
            </div>
            <!-- Supplier birthdate -->
            <div class="form-elt">
                <label for="birthdate" class="me-2 text-end"><?= $Localizer['Birthdate']; ?></label>
                <input id="birthdate" type="date" name="birthdate" class="ts-form-control-light me-2"/>
            </div>
            <!-- Supplier gender -->
            <div class="form-elt">
                <label for="genders" class="me-2 text-end"><?= $Localizer['Gender']; ?></label>
                <select id="genders" name="civilities[GenderId]" class="ts-form-control-light me-2">
                    <option data-value="" disabled selected><?= $Localizer['GenderSelect'] ?></option>
                    <?php
                    if (isset($civilities['Genders'])) {
                        $_genders = $civilities['Genders'];
                        $relations = null;
                        foreach ($_genders as $_gender) {
                            $relations[$_gender->It()->Id] = [
                                'Label' => $_gender->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label,
                                'Name' => $_gender->It()->Name
                            ];
                        }
                        uasort($relations, function($a, $b) {
                            if ($a['Name'] === 'Non applicable') return -1;
                            if ($b['Name'] === 'Non applicable') return 1;
                            return strcmp($a['Label'], $b['Label']);
                        });
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>" data-value="<?= $relation['Name']; ?>"><?= $relation['Label']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Supplier status -->
            <div class="form-elt">
                <label for="statuses" class="me-2 text-end"><?= $Localizer['Status']; ?></label>
                <select id="statuses" name="civilities[StatusId]" class="ts-form-control-light me-2">
                    <option data-value="" disabled selected><?= $Localizer['StatusSelect'] ?></option>
                    <?php
                    if (isset($civilities['Statuses'])) {
                        $_statuses = $civilities['Statuses'];
                        $relations = null;
                        foreach ($_statuses as $_status) {
                            $relations[$_status->It()->Id] = [
                                'Label' => $_status->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label,
                                'Name' => $_status->It()->Name
                            ];
                        }
                        uasort($relations, function($a, $b) {
                            if ($a['Name'] === 'Non applicable') return -1;
                            if ($b['Name'] === 'Non applicable') return 1;
                            return strcmp($a['Label'], $b['Label']);
                        });
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>" data-value="<?= $relation['Name']; ?>"><?= $relation['Label']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Supplier occupation -->
            <div class="form-elt">
                <label for="occupations" class="me-2 text-end"><?= $Localizer['Occupation']; ?></label>
                <select id="occupations" name="civilities[OccupationId]" class="ts-form-control-light me-2">
                    <option data-value="" disabled selected><?= $Localizer['OccupationSelect'] ?></option>
                    <?php
                    if (isset($civilities['Occupations'])) {
                        $_occupations = $civilities['Occupations'];
                        $relations = null;
                        foreach ($_occupations as $_occupation) {
                            $relations[$_occupation->It()->Id] = [
                                'Label' => $_occupation->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label,
                                'Name' => $_occupation->It()->Name
                            ];
                        }
                        uasort($relations, function($a, $b) {
                            if ($a['Name'] === 'Non applicable') return -1;
                            if ($b['Name'] === 'Non applicable') return 1;
                            return strcmp($a['Label'], $b['Label']);
                        });
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>" data-value="<?= $relation['Name']; ?>"><?= $relation['Label']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Supplier title -->
            <div class="form-elt">
                <label for="titles" class="me-2 text-end"><?= $Localizer['Title']; ?></label>
                <select id="titles" name="civilities[TitleId]" class="ts-form-control-light me-2">
                    <option data-value="" disabled selected><?= $Localizer['TitleSelect'] ?></option>
                    <?php
                    if (isset($civilities['Titles'])) {
                        $_titles = $civilities['Titles'];
                        $relations = null;
                        foreach ($_titles as $_title) {
                            $relations[$_title->It()->Id] = [
                                'Label' => $_title->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label,
                                'Name' => $_title->It()->Name
                            ];
                        }
                        uasort($relations, function($a, $b) {
                            if ($a['Name'] === 'Non applicable') return -1;
                            if ($b['Name'] === 'Non applicable') return 1;
                            return strcmp($a['Label'], $b['Label']);
                        });
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>" data-value="<?= $relation['Name']; ?>"><?= $relation['Label']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Supplier country -->
            <div class="form-elt">
                <label for="countryid" class="me-2 text-end"><?= $Localizer['Country']; ?></label>
                <select id="countryid" name="countryid" class="ts-form-control-light me-2">
                    <option data-value="" disabled selected><?= $Localizer['CountrySelect'] ?></option>
                    <?php
                    if (isset($countries)){
                        $relations = null;
                        foreach ($countries as $country){
                            $relations[$country->It()->Id] = [
                                'Label' => $country->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label,
                                'ISO3' => $country->It()->Iso3,
                            ];
                        }
                        uasort($relations, function($a, $b) {
                            return strcmp($a['Label'], $b['Label']);
                        });
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>" data-value="<?= $relation['ISO3']; ?>"><?= $relation['Label']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Supplier city -->
            <div class="form-elt">
                <label for="cityid" class="me-2 text-end"><?= $Localizer['City']; ?></label>
                <select id="cityid" name="cityid" class="ts-form-control-light me-2">
                    <option data-value="" disabled selected><?= $Localizer['CitySelect'] ?></option>
                    <?php
                    if (isset($cities)){
                        $relations = null;
                        foreach ($cities as $city){
                            $relations[$city->It()->Id] = [
                                'Label' => $city->LanguageRelations()->FirstOrDefault(fn($n) => $n->LangId == $langId)->Label,
                                'Name' => $city->It()->Name
                            ];
                        }
                        uasort($relations, function($a, $b) {
                            return strcmp($a['Label'], $b['Label']);
                        });
                        foreach ($relations as $key => $relation){
                            ?>
                            <option value="<?= $key ?>" data-value="<?= $relation['Name']; ?>"><?= $relation['Label']; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>
            <!-- Contacts -->
            <div class="form-elt">
                <label for="phone1" class="me-2 text-end"><?= $Localizer['Phone1']; ?></label>
                <input id="phone1" type="text" name="contacts[0][contact]" class="ts-form-control-light me-2"/>
                <input id="contacttype1" type="hidden" name="contacts[0][contacttypeid]" value="<?= $phoneId ?>"/>
                <input id="contactname1" type="hidden" name="contacts[0][contactname]" value="<?= $phone ?>1"/>
                <input id="contactphoto1" type="hidden" name="contacts[0][contactphoto]" value="<?= $phonePhoto ?>"/>
            </div>
            <!-- -->
            <div class="form-elt">
                <label for="phone2" class="me-2 text-end"><?= $Localizer['Phone2']; ?></label>
                <input id="phone2" type="text" name="contacts[1][contact]" class="ts-form-control-light me-2"/>
                <input id="contacttype2" type="hidden" name="contacts[1][contacttypeid]" value="<?= $phoneId ?>"/>
                <input id="contactname2" type="hidden" name="contacts[1][contactname]" value="<?= $phone ?>2"/>
                <input id="contactphoto2" type="hidden" name="contacts[1][contactphoto]" value="<?= $phonePhoto ?>"/>
            </div>
            <!-- -->
            <div class="form-elt">
                <label for="location" class="me-2 text-end"><?= $Localizer['Location']; ?></label>
                <input id="location" type="text" name="contacts[3][contact]" class="ts-form-control-light me-2"/>
                <input id="contacttype3" type="hidden" name="contacts[3][contacttypeid]" value="<?= $locationId ?>"/>
                <input id="contactname3" type="hidden" name="contacts[3][contactname]" value="<?= $location ?>1"/>
                <input id="contactphoto3" type="hidden" name="contacts[3][contactphoto]" value="<?= $locationPhoto ?>"/>
            </div>
            <!-- -->
            <div class="form-elt">
                <label for="email" class="me-2 text-end"><?= $Localizer['Email']; ?></label>
                <input id="email" type="text" name="contacts[4][contact]" class="ts-form-control-light me-2"/>
                <input id="contacttype3" type="hidden" name="contacts[4][contacttypeid]" value="<?= $emailId ?>"/>
                <input id="contactname3" type="hidden" name="contacts[4][contactname]" value="<?= $email ?>1"/>
                <input id="contactphoto3" type="hidden" name="contacts[4][contactphoto]" value="<?= $emailPhoto ?>"/>
            </div>
            <!-- Supplier btn -->
            <div class="form-elt">
                <button class="btn btn-success"><?= $Localizer['SupplierSuccessBtn'] ?></button>
            </div>
        </div>
    </form>
    <!-- -->
    <div id="supplier-list">
        <div class="title">
            <span><?= $Localizer['SupplierList']; ?></span>
        </div>
        <?php
        if (isset($suppliers)) {
            foreach ($suppliers as $supplier) {
                $fullname = null;
                if (!empty($supplier->Profile()->FullName()['MaidenName'])){
                    $fullname = $supplier->Profile()->FullName()['MaidenName'];
                    if (!empty($supplier->Profile()->FullName()['FirstName']))
                        $fullname .= ', '.$supplier->Profile()->FullName()['FirstName'];
                    $fullname .= ' '.$Localizer['Maiden'].' '.$supplier->Profile()->FullName()['LastName'];
                }else{
                    $fullname = $supplier->Profile()->FullName()['LastName'];
                    if (!empty($supplier->Profile()->FullName()['FirstName']))
                        $fullname .= ', '.$supplier->Profile()->FullName()['FirstName'];
                }
                ?>
                <div class="supplier-elt"><?= $fullname ?></div>
                <?php
            }
        }
        ?>
    </div>
</div>