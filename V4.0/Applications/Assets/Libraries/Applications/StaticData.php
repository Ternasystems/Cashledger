<?php
/* Static data */

// Import libraries
use TS_Configuration\Classes\XMLManager;
use TS_Utility\Classes\Utils;

$xmlConf = new XMLManager(dirname(__DIR__, 2).'\Data\Xml\config.xml');

// Instantiate Utilities
$utils = new Utils();

//Create and initialize the ViewData array
$ViewData =[
    'Icon' => $utils->getImage($xmlConf,'Icon'),
    'Logo' => $utils->getImage($xmlConf,'Logo'),
    'Logo_mobile' => $utils->getImage($xmlConf,'Logo_mobile'),
    'Jeoline_white' => $utils->getImage($xmlConf,'Jeoline_white'),
    'Social_Phone' => $utils->getImage($xmlConf,'Social_Phone'),
    'Social_Email' => $utils->getImage($xmlConf,'Social_Email'),
    'Social_Location' => $utils->getImage($xmlConf,'Social_Location'),
    'Logout' => $utils->getImage($xmlConf,'Logout'),
    'Settings' => $utils->getImage($xmlConf,'Settings'),
    'Files' => $utils->getImage($xmlConf,'Files'),
    'GoldJeon' => $utils->getImage($xmlConf,'GoldJeon'),
    'BlueJeon' => $utils->getImage($xmlConf,'BlueJeon'),
    'RedJeon' => $utils->getImage($xmlConf,'RedJeon'),
    'Accounting' => $utils->getImage($xmlConf,'Accounting'),
    'Billing' => $utils->getImage($xmlConf,'Billing'),
    'Booking' => $utils->getImage($xmlConf,'Booking'),
    'Control' => $utils->getImage($xmlConf,'Control'),
    'Dashboarding' => $utils->getImage($xmlConf,'Dashboarding'),
    'Emailing' => $utils->getImage($xmlConf,'Emailing'),
    'Forecasting' => $utils->getImage($xmlConf,'Forecasting'),
    'HRM' => $utils->getImage($xmlConf,'HRM'),
    'Hudel' => $utils->getImage($xmlConf,'Hudel'),
    'IDS' => $utils->getImage($xmlConf,'IDS'),
    'Inventory' => $utils->getImage($xmlConf,'Inventory'),
    'Invoicing' => $utils->getImage($xmlConf,'Invoicing'),
    'Meeting' => $utils->getImage($xmlConf,'Meeting'),
    'Messaging' => $utils->getImage($xmlConf,'Messaging'),
    'Partnership' => $utils->getImage($xmlConf,'Partnership'),
    'Payments' => $utils->getImage($xmlConf,'Payments'),
    'Payroll' => $utils->getImage($xmlConf,'Payroll'),
    'Profiling' => $utils->getImage($xmlConf,'Profiling'),
    'Publishing' => $utils->getImage($xmlConf,'Publishing'),
    'Purchase' => $utils->getImage($xmlConf,'Purchase'),
    'Reporting' => $utils->getImage($xmlConf,'Reporting'),
    'Tasks' => $utils->getImage($xmlConf,'Tasks'),
    'Teller' => $utils->getImage($xmlConf,'Teller'),
    'Wholesale' => $utils->getImage($xmlConf,'Wholesale'),
    'CompanyName' => $utils->getCompanyName($xmlConf),
    "Languages" => [
        "en-US" => $utils->getLanguage($xmlConf, 'en-US'),
        "en-GB" => $utils->getLanguage($xmlConf, 'en-GB'),
        "es-ES" => $utils->getLanguage($xmlConf, 'es-ES'),
        "ar-SA" => $utils->getLanguage($xmlConf, 'ar-SA'),
        "fr-FR" => $utils->getLanguage($xmlConf, 'fr-FR')
    ],
    'ConnectionString' => $utils->getDBConnectionData($xmlConf, 'PGSQL'),
    'DefaultLanguage' => $utils->getDefaultLanguage($xmlConf),
    'DefaultApp' => $utils->getDefaultApp($xmlConf)->getAttribute('name'),
    'DefaultController' => $utils->getDefaultApp($xmlConf)->getAttribute('controller'),
    'DefaultAction' => $utils->getDefaultApp($xmlConf)->getAttribute('action'),
    'IP' => $utils->getIP(),
    'cssPath' => $utils->getPath($xmlConf, 'css'),
    'jsPath' => $utils->getPath($xmlConf, 'js'),
    'vcPath' => $utils->getPath($xmlConf, 'vc'),
    'layoutPath' => $utils->getPath($xmlConf, 'layout'),
    'Bootstrap' => $utils->getPath($xmlConf, 'bootstrap'),
    'BootstrapIcon' => $utils->getPath($xmlConf, 'bicon'),
    'JQuery' => $utils->getPath($xmlConf, 'jquery'),
    'AjaxUnobstrusive' => $utils->getPath($xmlConf, 'unobstrusive'),
    'RxJS' => $utils->getPath($xmlConf, 'rxjs'),
    'CryptoJS' => $utils->getPath($xmlConf, 'crypto'),
    'GlobalJS' => $utils->getPath($xmlConf, 'global')
];