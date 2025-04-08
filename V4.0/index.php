<?php
/* Applications router */

require_once 'autoload.php';

include_once 'Applications/Assets/Libraries/applications/StaticData.php';

// Framework

//- DependencyLibrary
use TS_DependencyInjection\Classes\ApplicationBuilder;
use TS_DependencyInjection\Classes\ServiceLocator;

// APIs

//- Persistence

//-- DTO
use API_DTORepositories_Context\Context;
use API_DTORepositories_Context\DTOContext;
use API_DTORepositories\AppCategoryRepository;
use API_DTORepositories\AppRepository;
use API_DTORepositories\LanguageRepository;

//-- Inventory
use API_InventoryRepositories_Context\InventoryContext;
use API_InventoryRepositories\ManufacturerRepository;
use API_InventoryRepositories\PackagingRepository;
use API_InventoryRepositories\ProductAttributeRepository;
use API_InventoryRepositories\ProductCategoryRepository;
use API_InventoryRepositories\ProductRepository;
use API_InventoryRepositories\UnitRepository;
use API_InventoryRepositories\WarehouseRepository;

//-- Profiling
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Context\TokenContext;
use API_ProfilingRepositories\CivilityRepository;
use API_ProfilingRepositories\ContactRepository;
use API_ProfilingRepositories\ContactTypeRepository;
use API_ProfilingRepositories\CredentialRepository;
use API_ProfilingRepositories\GenderRepository;
use API_ProfilingRepositories\OccupationRepository;
use API_ProfilingRepositories\PermissionRepository;
use API_ProfilingRepositories\ProfileRepository;
use API_ProfilingRepositories\RoleRepository;
use API_ProfilingRepositories\StatusRepository;
use API_ProfilingRepositories\TitleRepository;
use API_ProfilingRepositories\TokenRepository;

//-- Relations
use API_RelationRepositories_Context\RelationContext;
use API_RelationRepositories\AppRelationRepository;
use API_RelationRepositories\AttributeRelationRepository;
use API_RelationRepositories\CivilityRelationRepository;
use API_RelationRepositories\ContactRelationRepository;
use API_RelationRepositories\GenderRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories\OccupationRelationRepository;
use API_RelationRepositories\RoleRelationRepository;
use API_RelationRepositories\StatusRelationRepository;
use API_RelationRepositories\TitleRelationRepository;

//- Supporting

//-- Administration
use API_DTOEntities_Factory\AppFactory;

//-- Inventory
use API_InventoryEntities_Factory\ProductAttributeFactory;
use API_InventoryEntities_Factory\ProductFactory;

//-- Profiling
use API_ProfilingEntities_Factory\CivilityFactory;
use API_ProfilingEntities_Factory\ContactFactory;
use API_ProfilingEntities_Factory\CredentialFactory;
use API_ProfilingEntities_Factory\GenderFactory;
use API_ProfilingEntities_Factory\OccupationFactory;
use API_ProfilingEntities_Factory\ProfileFactory;
use API_ProfilingEntities_Factory\RoleFactory;
use API_ProfilingEntities_Factory\StatusFactory;
use API_ProfilingEntities_Factory\TitleFactory;
use API_ProfilingEntities_Factory\TokenFactory;

//- Services

//-- Administration
use API_Administration_Contract\IAppService;
use API_Administration_Contract\ILanguageService;
use API_Administration_Contract\IParameterService;
use API_Administration_Service\AppService;
use API_Administration_Service\LanguageService;
use API_Administration_Service\ParameterService;
use API_Administration_Controller\AppController;
use API_Administration_Controller\LanguageController;
use API_Administration_Controller\ParameterController;

//-- Inventory
use API_Inventory_Contract\IManufacturerService;
use API_Inventory_Contract\IPackagingService;
use API_Inventory_Contract\IProductService;
use API_Inventory_Contract\IUnitService;
use API_Inventory_Contract\IWarehouseService;
use API_Inventory_Service\ManufacturerService;
use API_Inventory_Service\PackagingService;
use API_Inventory_Service\ProductService;
use API_Inventory_Service\UnitService;
use API_Inventory_Service\WarehouseService;
use API_Inventory_Controller\ManufacturerController;
use API_Inventory_Controller\PackagingController;
use API_Inventory_Controller\ProductController;
use API_Inventory_Controller\UnitController;
use API_Inventory_Controller\WarehouseController;

//-- Profiling
use API_Profiling_Contract\IAuthenticationService;
use API_Profiling_Contract\ICredentialService;
use API_Profiling_Service\AuthenticationService;
use API_Profiling_Service\CredentialService;
use API_Profiling_Controller\AuthenticationController;

// Applications

//- Accounting
use APP_Accounting_Controller\HomeController as AccountingHomeController;

//- Administration
use APP_Administration_Controller\AdministrationController as AdministrationController;

//- Billing
use APP_Billing_Controller\HomeController as BillingHomeController;

//- Booking
use APP_Booking_Controller\HomeController as BookingHomeController;

//- Control
use APP_Control_Controller\HomeController as ControlHomeController;

//- Dashboarding
use APP_Dashboarding_Controller\HomeController as DashboardingHomeController;

//- Emailing
use APP_Emailing_Controller\HomeController as EmailingHomeController;

//- Forecasting
use APP_Forecasting_Controller\HomeController as ForecastingHomeController;

//- HRM
use APP_HRM_Controller\HomeController as HRMHomeController;

//- Hudel
use APP_Hudel_Controller\HomeController as HudelHomeController;

//- IDS
use APP_IDS_Controller\HomeController as IDSHomeController;

//- Inventory
use APP_Inventory_Controller\ConfigController as InventoryConfigController;
use APP_Inventory_Controller\HomeController as InventoryHomeController;

//- Invoicing
use APP_Invoicing_Controller\HomeController as InvoicingHomeController;

//- Meeting
use APP_Meeting_Controller\HomeController as MeetingHomeController;

//- Messaging
use APP_Messaging_Controller\HomeController as MessagingHomeController;

//- Partnership
use APP_Partnership_Controller\HomeController as PartnershipHomeController;

//- Payments
use APP_Payments_Controller\HomeController as PaymentsHomeController;

//- Payroll
use APP_Payroll_Controller\HomeController as PayrollHomeController;

//- Presentation
use APP_Presentation_Controller\DashboardController as PresentationDashboardController;
use APP_Presentation_Controller\HomeController as PresentationHomeController;

//- Profiling
use APP_Profiling_Controller\HomeController as ProfilingHomeController;

//- Publishing
use APP_Publishing_Controller\HomeController as PublishingHomeController;

//- Purchase
use APP_Purchase_Controller\HomeController as PurchaseHomeController;

//- Reporting
use APP_Reporting_Controller\HomeController as ReportingHomeController;

//- Tasks
use APP_Tasks_Controller\HomeController as TasksHomeController;

//- Teller
use APP_Teller_Controller\HomeController as TellerHomeController;

//- Wholesale
use APP_Wholesale_Controller\HomeController as WholesaleHomeController;

if (session_status() == PHP_SESSION_NONE)
    session_start();

// Check Query strings
if (empty($_SERVER['QUERY_STRING'])){
    // Get default URI
    $lang = $ViewData['DefaultLanguage'];
    $app = $ViewData['DefaultApp'];
    $ctrl = $ViewData['DefaultController'];
    $action = $ViewData['DefaultAction'];

    header('Location: '.$lang.'/'.$app.'/'.$ctrl.'/'.$action);
}

// Create application
$builder = new ApplicationBuilder();

// Add configurations
$builder->AddConfigurations(['ConnectionString' => $ViewData['ConnectionString']]);

// Add services

// APIs

//- Contexts
$builder->AddDBContext(Context::class, Context::class);
$builder->AddDBContext(DTOContext::class, DTOContext::class);
$builder->AddDBContext(InventoryContext::class, InventoryContext::class);
$builder->AddDBContext(ProfilingContext::class, ProfilingContext::class);
$builder->AddDBContext(TokenContext::class, TokenContext::class);
$builder->AddDBContext(RelationContext::class, RelationContext::class);

//- Persistence

//-- DTO
$builder->AddScoped(AppCategoryRepository::class, AppCategoryRepository::class);
$builder->AddScoped(AppRepository::class, AppRepository::class);
$builder->AddScoped(LanguageRepository::class, LanguageRepository::class);

//-- Inventory
$builder->AddScoped(ManufacturerRepository::class, ManufacturerRepository::class);
$builder->AddScoped(PackagingRepository::class, PackagingRepository::class);
$builder->AddScoped(ProductAttributeRepository::class, ProductAttributeRepository::class);
$builder->AddScoped(ProductCategoryRepository::class, ProductCategoryRepository::class);
$builder->AddScoped(ProductRepository::class, ProductRepository::class);
$builder->AddScoped(UnitRepository::class, UnitRepository::class);
$builder->AddScoped(WarehouseRepository::class, WarehouseRepository::class);

//-- Profiling
$builder->AddScoped(CivilityRepository::class, CivilityRepository::class);
$builder->AddScoped(ContactRepository::class, ContactRepository::class);
$builder->AddScoped(ContactTypeRepository::class, ContactTypeRepository::class);
$builder->AddScoped(CredentialRepository::class, CredentialRepository::class);
$builder->AddScoped(GenderRepository::class, GenderRepository::class);
$builder->AddScoped(OccupationRepository::class, OccupationRepository::class);
$builder->AddScoped(PermissionRepository::class, PermissionRepository::class);
$builder->AddScoped(ProfileRepository::class, ProfileRepository::class);
$builder->AddScoped(RoleRepository::class, RoleRepository::class);
$builder->AddScoped(StatusRepository::class, StatusRepository::class);
$builder->AddScoped(TitleRepository::class, TitleRepository::class);
$builder->AddScoped(TokenRepository::class, TokenRepository::class);

//-- Relations
$builder->AddScoped(AppRelationRepository::class, AppRelationRepository::class);
$builder->AddScoped(AttributeRelationRepository::class, AttributeRelationRepository::class);
$builder->AddScoped(CivilityRelationRepository::class, CivilityRelationRepository::class);
$builder->AddScoped(ContactRelationRepository::class, ContactRelationRepository::class);
$builder->AddScoped(GenderRelationRepository::class, GenderRelationRepository::class);
$builder->AddScoped(LanguageRelationRepository::class, LanguageRelationRepository::class);
$builder->AddScoped(OccupationRelationRepository::class, OccupationRelationRepository::class);
$builder->AddScoped(RoleRelationRepository::class, RoleRelationRepository::class);
$builder->AddScoped(StatusRelationRepository::class, StatusRelationRepository::class);
$builder->AddScoped(TitleRelationRepository::class, TitleRelationRepository::class);

//- Supporting

//-- Administration
$builder->AddScoped(AppFactory::class, AppFactory::class);

//-- Inventory
$builder->AddScoped(ProductAttributeFactory::class, ProductAttributeFactory::class);
$builder->AddScoped(ProductFactory::class, ProductFactory::class);

//-- Profiling
$builder->AddScoped(CivilityFactory::class, CivilityFactory::class);
$builder->AddScoped(ContactFactory::class, ContactFactory::class);
$builder->AddScoped(CredentialFactory::class, CredentialFactory::class);
$builder->AddScoped(GenderFactory::class, GenderFactory::class);
$builder->AddScoped(OccupationFactory::class, OccupationFactory::class);
$builder->AddScoped(ProfileFactory::class, ProfileFactory::class);
$builder->AddScoped(RoleFactory::class, RoleFactory::class);
$builder->AddScoped(StatusFactory::class, StatusFactory::class);
$builder->AddScoped(TitleFactory::class, TitleFactory::class);
$builder->AddScoped(TokenFactory::class, TokenFactory::class);

//- Services

//-- Administration
$builder->AddScoped(IAppService::class, AppService::class);
$builder->AddScoped(ILanguageService::class, LanguageService::class);
$builder->AddScoped(IParameterService::class, ParameterService::class);
$builder->AddScoped(AppController::class, AppController::class);
$builder->AddScoped(LanguageController::class, LanguageController::class);
$builder->AddScoped(ParameterController::class, ParameterController::class);

//-- Inventory
$builder->AddScoped(IManufacturerService::class, ManufacturerService::class);
$builder->AddScoped(IPackagingService::class, PackagingService::class);
$builder->AddScoped(IProductService::class, ProductService::class);
$builder->AddScoped(IUnitService::class, UnitService::class);
$builder->AddScoped(IWarehouseService::class, WarehouseService::class);
$builder->AddScoped(ManufacturerController::class, ManufacturerController::class);
$builder->AddScoped(PackagingController::class, PackagingController::class);
$builder->AddScoped(ProductController::class, ProductController::class);
$builder->AddScoped(UnitController::class, UnitController::class);
$builder->AddScoped(WarehouseController::class, WarehouseController::class);

//-- Profiling
$builder->AddScoped(IAuthenticationService::class, AuthenticationService::class);
$builder->AddScoped(ICredentialService::class, CredentialService::class);
$builder->AddScoped(AuthenticationController::class, AuthenticationController::class);

//- Applications

//-- Accounting
$builder->AddTransient(AccountingHomeController::class, AccountingHomeController::class);

//-- Administration
$builder->AddTransient(AdministrationController::class, AdministrationController::class);

//-- Billing
$builder->AddTransient(BillingHomeController::class, BillingHomeController::class);

//-- Booking
$builder->AddTransient(BookingHomeController::class, BookingHomeController::class);

//-- Control
$builder->AddTransient(ControlHomeController::class, ControlHomeController::class);

//-- Dashboarding
$builder->AddTransient(DashboardingHomeController::class, DashboardingHomeController::class);

//-- Emailing
$builder->AddTransient(EmailingHomeController::class, EmailingHomeController::class);

//-- Forecasting
$builder->AddTransient(ForecastingHomeController::class, ForecastingHomeController::class);

//-- HRM
$builder->AddTransient(HRMHomeController::class, HRMHomeController::class);

//-- Hudel
$builder->AddTransient(HudelHomeController::class, HudelHomeController::class);

//-- IDS
$builder->AddTransient(IDSHomeController::class, IDSHomeController::class);

//-- Inventory
$builder->AddTransient(InventoryConfigController::class, InventoryConfigController::class);
$builder->AddTransient(InventoryHomeController::class, InventoryHomeController::class);

//-- Invoicing
$builder->AddTransient(InvoicingHomeController::class, InvoicingHomeController::class);

//-- Meeting
$builder->AddTransient(MeetingHomeController::class, MeetingHomeController::class);

//-- Messaging
$builder->AddTransient(MessagingHomeController::class, MessagingHomeController::class);

//-- Partnership
$builder->AddTransient(PartnershipHomeController::class, PartnershipHomeController::class);

//-- Payments
$builder->AddTransient(PaymentsHomeController::class, PaymentsHomeController::class);

//-- Payroll
$builder->AddTransient(PayrollHomeController::class, PayrollHomeController::class);

//-- Presentation
$builder->AddTransient(PresentationDashboardController::class, PresentationDashboardController::class);
$builder->AddTransient(PresentationHomeController::class, PresentationHomeController::class);

//-- Profiling
$builder->AddTransient(ProfilingHomeController::class, ProfilingHomeController::class);

//-- Publishing
$builder->AddTransient(PublishingHomeController::class, PublishingHomeController::class);

//-- Purchase
$builder->AddTransient(PurchaseHomeController::class, PurchaseHomeController::class);

//-- Reporting
$builder->AddTransient(ReportingHomeController::class, ReportingHomeController::class);

//-- Tasks
$builder->AddTransient(TasksHomeController::class, TasksHomeController::class);

//-- Teller
$builder->AddTransient(TellerHomeController::class, TellerHomeController::class);

//-- Wholesale
$builder->AddTransient(WholesaleHomeController::class, WholesaleHomeController::class);

// Build application
$application = $builder->Build();

// Register service locator
ServiceLocator::SetApplication($application);

// Get the Query strings
$lang = $_GET['lang'] ?? $ViewData['DefaultLanguage'];
$app = $_GET['app'] ?? $ViewData['DefaultApp'];
$ctrl = $_GET['ctrl'] ?? $ViewData['DefaultController'];
$action = $_GET['action'] ?? $ViewData['DefaultAction'];
$ViewData['CurrentLanguage'] = $lang;

// Set the controller
$controller = ucfirst($ctrl).'Controller';

// Check if controller exists
$ctrlFile = 'Applications/'.$app.'/Controllers/'.$controller.'.php';

if (!file_exists($ctrlFile))
    throw new InvalidArgumentException('Controller '.$controller.' does not exist.');

require_once $ctrlFile;

// Check if controller class exists
// Set the fully qualified class name for the controller
$controller = 'APP_'.ucfirst($app).'_Controller\\'.ucfirst($ctrl).'Controller';

if (!class_exists($controller))
    throw new InvalidArgumentException('Controller '.$controller.' does not exist.');

$controller = $application->GetController($controller);

// Check if method exists in controller
if (!method_exists($controller, $action))
    throw new InvalidArgumentException('Action '.$action.' does not exist.');

$controller->callAction($action);
