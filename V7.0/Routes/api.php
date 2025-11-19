<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the `api.php` bootstrap file.
|
*/

use TS_Http\Classes\Router;
use TS_DependencyInjection\Classes\ServiceLocator;

// API Controllers
use API_Administration_Controller\AppController;
use API_Administration_Controller\AuditController;
use API_Administration_Controller\CountryController;
use API_Administration_Controller\LanguageController;
use API_Administration_Controller\ParameterController;
use API_Billing_Controller\CurrencyController;
use API_Billing_Controller\DiscountController;
use API_Billing_Controller\PriceController;
use API_Hrm_Controller\EmployeeController;
use API_Inventory_Controller\ProductController;
use API_Inventory_Controller\StockController;
use API_Invoicing_Controller\CustomerController;
use API_Payments_Controller\PaymentController;
use API_Profiling_Controller\AuthenticationController;
use API_Profiling_Controller\ContactController;
use API_Profiling_Controller\CredentialController;
use API_Profiling_Controller\ProfileController;
use API_Purchase_Controller\SupplierController;
use API_Taxes_Controller\TaxController;
use API_Teller_Controller\CashFigureController;
use API_Teller_Controller\TellerAuditController;
use API_Teller_Controller\TellerController;
use API_Teller_Controller\TellerSessionController;

/** @var Router $router */
$router = ServiceLocator::get(Router::class);
$router->prefix('/api'); // All routes in this file will start with /api

// --- Administration Routes ---
// By adding a 'defaults' array, we tell the AbstractController
// what resource it's currently handling.
$router->resource('/administration/apps', AppController::class, ['defaults' => ['resourceType' => 'App']]);
$router->resource('/administration/appcategories', AppController::class, ['defaults' => ['resourceType' => 'AppCategory']]);
$router->resource('/administration/countries', CountryController::class, ['defaults' => ['resourceType' => 'Country']]);
$router->resource('/administration/continents', CountryController::class, ['defaults' => ['resourceType' => 'Continent']]);
$router->resource('/administration/cities', CountryController::class, ['defaults' => ['resourceType' => 'City']]);
$router->resource('/administration/languages', LanguageController::class); // defaultResourceType is 'Language'
$router->resource('/administration/audits', AuditController::class); // defaultResourceType is 'Audit'

// --- Special Case: Parameter Controller ---
$router->get('/administration/parameter', [ParameterController::class, 'show']);
$router->put('/administration/parameter', [ParameterController::class, 'update']);
$router->get('/administration/parameter/from', [ParameterController::class, 'getFrom']);
$router->get('/administration/parameter/check', [ParameterController::class, 'check']);

// --- Billing Routes ---
$router->resource('/billing/currencies', CurrencyController::class);
$router->resource('/billing/discounts', DiscountController::class);
$router->resource('/billing/prices', PriceController::class);

// --- HRM Routes ---
$router->resource('/hrm/employees', EmployeeController::class);

// --- Inventory Routes ---
$router->resource('/inventory/products', ProductController::class, ['defaults' => ['resourceType' => 'Product']]);
$router->resource('/inventory/packagings', ProductController::class, ['defaults' => ['resourceType' => 'Packaging']]);
$router->resource('/inventory/units', ProductController::class, ['defaults' => ['resourceType' => 'Unit']]);
$router->resource('/inventory/stock', StockController::class);

// --- Invoicing Routes ---
$router->resource('/invoicing/customers', CustomerController::class);

// --- Payments Routes ---
$router->resource('/payments/methods', PaymentController::class);

// --- Profiling Routes ---
$router->resource('/profiling/profiles', ProfileController::class, ['defaults' => ['resourceType' => 'Profile']]);
$router->resource('/profiling/civilities', ProfileController::class, ['defaults' => ['resourceType' => 'Civility']]);
$router->resource('/profiling/genders', ProfileController::class, ['defaults' => ['resourceType' => 'Gender']]);
$router->resource('/profiling/occupations', ProfileController::class, ['defaults' => ['resourceType' => 'Occupation']]);
$router->resource('/profiling/statuses', ProfileController::class, ['defaults' => ['resourceType' => 'Status']]);
$router->resource('/profiling/titles', ProfileController::class, ['defaults' => ['resourceType' => 'Title']]);

$router->resource('/profiling/contacts', ContactController::class, ['defaults' => ['resourceType' => 'Contact']]);
$router->resource('/profiling/contacttypes', ContactController::class, ['defaults' => ['resourceType' => 'ContactType']]);

$router->resource('/profiling/credentials', CredentialController::class);
// Special routes for non-standard actions
$router->put('/profiling/credentials/{id}/password', [CredentialController::class, 'password']);

// --- Special Case: Authentication Controller ---
$router->post('/profiling/login', [AuthenticationController::class, 'login']);
$router->post('/profiling/logout', [AuthenticationController::class, 'logout']);
$router->get('/profiling/session', [AuthenticationController::class, 'session']);

// --- Purchase Routes ---
$router->resource('/purchase/suppliers', SupplierController::class);

// --- Taxes Routes ---
$router->resource('/taxes/taxes', TaxController::class); // e.g., /api/taxes/taxes

// --- Teller Routes ---
$router->resource('/teller/tellers', TellerController::class);
$router->resource('/teller/cashfigures', CashFigureController::class);
$router->resource('/teller/audits', TellerAuditController::class, ['defaults' => ['resourceType' => 'TellerAudit']]);
$router->resource('/teller/reversals', TellerAuditController::class, ['defaults' => ['resourceType' => 'TellerReversal']]);

$router->resource('/teller/sessions', TellerSessionController::class, ['defaults' => ['resourceType' => 'TellerSession']]);
$router->resource('/teller/transactions', TellerSessionController::class, ['defaults' => ['resourceType' => 'TellerTransaction']]);
$router->resource('/teller/payments', TellerSessionController::class, ['defaults' => ['resourceType' => 'TellerPayment']]);
$router->resource('/teller/receipts', TellerSessionController::class, ['defaults' => ['resourceType' => 'TellerReceipt']]);
$router->resource('/teller/transfers', TellerSessionController::class, ['defaults' => ['resourceType' => 'TellerTransfer']]);
$router->resource('/teller/cashcounts', TellerSessionController::class, ['defaults' => ['resourceType' => 'TellerCashCount']]);