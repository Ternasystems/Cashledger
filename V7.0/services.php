<?php
/*
|--------------------------------------------------------------------------
| Register Application Services
|--------------------------------------------------------------------------
|
| This file is responsible for building and configuring the main
| application DI container. It returns the built Application instance
| to the bootstrap file (index.php).
|
*/

declare(strict_types=1);

// Framework
use TS_Database\Classes\DBCredentials;
use TS_DependencyInjection\Classes\ApplicationBuilder;
use TS_DependencyInjection\Classes\Application;
use TS_Cache\Classes\CacheManager;
use TS_Cache\Classes\FileCacheAdapter;
use TS_Cache\Interfaces\ICacheAdapter;
use TS_Configuration\Classes\StreamFileHandler;
use TS_Configuration\Interfaces\ILogHandler;
use TS_Configuration\Interfaces\ILogger;
use TS_Configuration\Classes\Logger;
use TS_DependencyInjection\Classes\ServiceLocator;
use TS_Exception\Classes\CacheException;
use TS_Http\Classes\Request;
use TS_Http\Classes\Router;
use TS_Controller\Classes\ActionFilterExecutor;
use TS_Controller\Classes\FilterRegistry;
use TS_View\Classes\View;
use TS_View\Classes\ComponentService;
use TS_View\Classes\Escaper;
use TS_View\Classes\HelpersRegistry;
use TS_Http\Classes\FlashMessageService;
use TS_Utility\Classes\UrlGenerator;
use TS_Locale\Classes\Translator;
use TS_Locale\Classes\JsonTranslationLoader;
use TS_Locale\Classes\XmlTranslationLoader;
use TS_Configuration\Classes\XMLManager;
use TS_Configuration\Classes\ConfigurationService;
use TS_Database\Classes\DBContext;

// APIs - Persistence
use API_BillingRepositories\CurrencyRepository;
use API_BillingRepositories\DiscountRepository;
use API_BillingRepositories\PriceRepository;
use API_BillingRepositories_Context\BillingContext;
use API_DTORepositories\AppCategoryRepository;
use API_DTORepositories\AppRepository;
use API_DTORepositories\AuditRepository;
use API_DTORepositories\CityRepository;
use API_DTORepositories\ContinentRepository;
use API_DTORepositories\CountryRepository;
use API_DTORepositories\LanguageRepository;
use API_DTORepositories\ParameterRepository;
use API_DTORepositories_Context\DTOContext;
use API_HrmRepositories\EmployeeRepository;
use API_HrmRepositories_Context\HrmContext;
use API_InventoryRepositories\PackagingRepository;
use API_InventoryRepositories\ProductCategoryRepository;
use API_InventoryRepositories\ProductRepository;
use API_InventoryRepositories\StockRepository;
use API_InventoryRepositories\UnitRepository;
use API_InventoryRepositories_Context\InventoryContext;
use API_InvoicingRepositories\CustomerRepository;
use API_InvoicingRepositories_Context\InvoicingContext;
use API_PaymentsRepositories\PaymentMethodRepository;
use API_PaymentsRepositories_Context\PaymentsContext;
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
use API_ProfilingRepositories\TrackingRepository;
use API_ProfilingRepositories_Context\ProfilingContext;
use API_ProfilingRepositories_Context\TokenContext;
use API_PurchaseRepositories\SupplierRepository;
use API_PurchaseRepositories_Context\PurchaseContext;
use API_RelationRepositories\AppRelationRepository;
use API_RelationRepositories\CashRelationRepository;
use API_RelationRepositories\CivilityRelationRepository;
use API_RelationRepositories\ContactRelationRepository;
use API_RelationRepositories\GenderRelationRepository;
use API_RelationRepositories\LanguageRelationRepository;
use API_RelationRepositories\OccupationRelationRepository;
use API_RelationRepositories\ParameterRelationRepository;
use API_RelationRepositories\PriceRelationRepository;
use API_RelationRepositories\RoleRelationRepository;
use API_RelationRepositories\StatusRelationRepository;
use API_RelationRepositories\TitleRelationRepository;
use API_RelationRepositories_Context\RelationContext;
use API_TaxesRepositories\TaxRepository;
use API_TaxesRepositories_Context\TaxesContext;
use API_TellerRepositories\CashFigureRepository;
use API_TellerRepositories\TellerAuditRepository;
use API_TellerRepositories\TellerCashCountRepository;
use API_TellerRepositories\TellerPaymentRepository;
use API_TellerRepositories\TellerReceiptRepository;
use API_TellerRepositories\TellerRepository;
use API_TellerRepositories\TellerReversalRepository;
use API_TellerRepositories\TellerSessionRepository;
use API_TellerRepositories\TellerTransactionRepository;
use API_TellerRepositories\TellerTransferRepository;
use API_TellerRepositories_Context\TellerContext;

// APIs - Supporting
use API_BillingEntities_Factory\PriceFactory;
use API_DTOEntities_Factory\AppFactory;
use API_DTOEntities_Factory\CityFactory;
use API_DTOEntities_Factory\CollectableFactory;
use API_DTOEntities_Factory\CountryFactory;
use API_DTOEntities_Factory\ParameterFactory;
use API_HrmEntities_Factory\EmployeeFactory;
use API_InventoryEntities_Factory\ProductFactory;
use API_InventoryEntities_Factory\StockFactory;
use API_InvoicingEntities_Factory\CustomerFactory;
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
use API_ProfilingEntities_Factory\TrackingFactory;
use API_PurchaseEntities_Factory\SupplierFactory;
use API_TellerEntities_Factory\TellerCashCountFactory;
use API_TellerEntities_Factory\TellerFactory;
use API_TellerEntities_Factory\TellerPaymentFactory;
use API_TellerEntities_Factory\TellerReceiptFactory;
use API_TellerEntities_Factory\TellerReversalFactory;
use API_TellerEntities_Factory\TellerSessionFactory;
use API_TellerEntities_Factory\TellerTransactionFactory;
use API_TellerEntities_Factory\TellerTransferFactory;

// APIs - Services (Contracts)
use API_Administration_Contract\IAppCategoryService;
use API_Administration_Contract\IAppService;
use API_Administration_Contract\IAuditService;
use API_Administration_Contract\ICityService;
use API_Administration_Contract\IContinentService;
use API_Administration_Contract\ICountryService;
use API_Administration_Contract\ILanguageService;
use API_Administration_Contract\IParameterService;
use API_Billing_Contract\ICurrencyService;
use API_Billing_Contract\IDiscountService;
use API_Billing_Contract\IPriceService;
use API_Hrm_Contract\IEmployeeService;
use API_Inventory_Contract\IPackagingService;
use API_Inventory_Contract\IProductService;
use API_Inventory_Contract\IStockService;
use API_Inventory_Contract\IUnitService;
use API_Invoicing_Contract\ICustomerService;
use API_Payments_Contract\IPaymentMethodService;
use API_Profiling_Contract\IAuthenticationService;
use API_Profiling_Contract\ICivilityService;
use API_Profiling_Contract\IContactService;
use API_Profiling_Contract\IContactTypeService;
use API_Profiling_Contract\ICredentialService;
use API_Profiling_Contract\IGenderService;
use API_Profiling_Contract\IOccupationService;
use API_Profiling_Contract\IProfileService;
use API_Profiling_Contract\IStatusService;
use API_Profiling_Contract\ITitleService;
use API_Purchase_Contract\ISupplierService;
use API_Taxes_Contract\ITaxService;
use API_Teller_Contract\ICashFigureService;
use API_Teller_Contract\ITellerAuditService;
use API_Teller_Contract\ITellerCashCountService;
use API_Teller_Contract\ITellerPaymentService;
use API_Teller_Contract\ITellerReceiptService;
use API_Teller_Contract\ITellerReversalService;
use API_Teller_Contract\ITellerService;
use API_Teller_Contract\ITellerSessionService;
use API_Teller_Contract\ITellerTransactionService;
use API_Teller_Contract\ITellerTransferService;

// APIs - Services (Implementations)
use API_Administration_Service\AppCategoryService;
use API_Administration_Service\AppService;
use API_Administration_Service\AuditService;
use API_Administration_Service\CityService;
use API_Administration_Service\ContinentService;
use API_Administration_Service\CountryService;
use API_Administration_Service\LanguageService;
use API_Administration_Service\ParameterService;
use API_Billing_Service\CurrencyService;
use API_Billing_Service\DiscountService;
use API_Billing_Service\PriceService;
use API_Hrm_Service\EmployeeService;
use API_Inventory_Service\PackagingService;
use API_Inventory_Service\ProductService;
use API_Inventory_Service\StockService;
use API_Inventory_Service\UnitService;
use API_Invoicing_Service\CustomerService;
use API_Payments_Service\PaymentMethodService;
use API_Profiling_Service\AuthenticationService;
use API_Profiling_Service\CivilityService;
use API_Profiling_Service\ContactService;
use API_Profiling_Service\ContactTypeService;
use API_Profiling_Service\CredentialService;
use API_Profiling_Service\GenderService;
use API_Profiling_Service\OccupationService;
use API_Profiling_Service\ProfileService;
use API_Profiling_Service\StatusService;
use API_Profiling_Service\TitleService;
use API_Purchase_Service\SupplierService;
use API_Taxes_Service\TaxService;
use API_Teller_Service\CashFigureService;
use API_Teller_Service\TellerAuditService;
use API_Teller_Service\TellerCashCountService;
use API_Teller_Service\TellerPaymentService;
use API_Teller_Service\TellerReceiptService;
use API_Teller_Service\TellerReversalService;
use API_Teller_Service\TellerService;
use API_Teller_Service\TellerSessionService;
use API_Teller_Service\TellerTransactionService;
use API_Teller_Service\TellerTransferService;

// APIs - Services (Facades)
use API_Administration_Facade\AppFacade;
use API_Administration_Facade\AuditFacade;
use API_Administration_Facade\CountryFacade;
use API_Administration_Facade\LanguageFacade;
use API_Billing_Facade\CurrencyFacade;
use API_Billing_Facade\DiscountFacade;
use API_Billing_Facade\PriceFacade;
use API_Hrm_Facade\EmployeeFacade;
use API_Inventory_Facade\ProductFacade;
use API_Inventory_Facade\StockFacade;
use API_Invoicing_Facade\CustomerFacade;
use API_Payments_Facade\PaymentFacade;
use API_Profiling_Facade\ContactFacade;
use API_Profiling_Facade\CredentialFacade;
use API_Profiling_Facade\ProfileFacade;
use API_Purchase_Facade\SupplierFacade;
use API_Taxes_Facade\TaxFacade;
use API_Teller_Facade\CashFigureFacade;
use API_Teller_Facade\TellerAuditFacade;
use API_Teller_Facade\TellerFacade;
use API_Teller_Facade\TellerSessionFacade;

// APIs - Services (Controllers)
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

// Applications
use APP_Administration_Controller\HomeController as AdministrationController;
use App_Presentation_Controller\HomeController as PresentationHomeController;

try {
    // === 1. Setup Caching ===
    $cacheAdapter = new FileCacheAdapter(__DIR__ . '/Framework/CPF/Assets/Cache');
    $cacheManager = new CacheManager($cacheAdapter);

    // === 2. Create the Application Builder ===
    $builder = new ApplicationBuilder($cacheManager);

    // === 3. Register Core Configuration ===

    // Register the XMLManager for the config file as a Singleton
    $builder->addSingleton(XMLManager::class, fn() => new XMLManager(__DIR__ . '/Applications/Assets/Data/Xml/config.xml'));

    // Register our new ConfigurationService as a Singleton
    // It automatically gets the XMLManager injected.
    $builder->addSingleton(ConfigurationService::class, ConfigurationService::class);

    // === 4. Register Core Framework Services ===

    // Register the Application itself
    $builder->addScoped(Application::class, fn(Application $app) => $app);

    // Caching
    $builder->addScoped(ICacheAdapter::class, fn() => $cacheAdapter);
    $builder->addScoped(CacheManager::class, fn() => $cacheManager);

    // Logging
    $builder->addSingleton(ILogHandler::class, fn() => new StreamFileHandler(__DIR__ . '/Framework/CPF/Assets/Logs', 'cashledger'));
    $builder->addScoped(ILogger::class, Logger::class);

    // Database
    $builder->addScoped(DBCredentials::class, function(Application $app) {
        /** @var ConfigurationService $config */
        $config = $app->get(ConfigurationService::class);
        $credentials = $config->getDbCredentials();

        if (!$credentials) {
            throw new Exception("Database credentials not found in config.xml");
        }
        return $credentials;
    });
    $builder->addScoped(DBContext::class, DBContext::class);

    // HTTP / Routing
    $builder->addScoped(Router::class, Router::class);
    $builder->addScoped(ActionFilterExecutor::class, ActionFilterExecutor::class);
    $builder->addSingleton(FilterRegistry::class, FilterRegistry::class);
    $builder->addScoped(FlashMessageService::class, FlashMessageService::class);

    // URL Generation
    $urlConfig = [
        'pattern' => '/{lang}/{application}/{controller}/{action}',
        'default' => [
            'lang' => 'en-US', // Default, will be overridden
            'application' => 'Presentation',
            'controller' => 'Home',
            'action' => 'index'
        ],
        'base_uri' => '/cashledger'
    ];
    $builder->addSingleton(UrlGenerator::class, fn(Application $app) => new UrlGenerator($urlConfig));


    // Locale / Translation
    $builder->addScoped(Translator::class, function (Application $app) {

        /** @var ConfigurationService $config */
        $config = $app->get(ConfigurationService::class);
        $defaultLang = $config->getDefaultLanguage();

        $request = Request::createFromGlobals();
        $path = $request->getPath();
        $currentLang = $defaultLang;

        if (str_starts_with($path, '/api') || str_starts_with($path, '/api.php')) {
            $currentLang = $request->getQuery('lang', $defaultLang);
        } else {
            $currentLang = $request->getSegment(1) ?? $defaultLang;
        }

        $translator = new Translator($currentLang, $defaultLang);

        // Register all our known translation directories
        $translator->addLoader(new XmlTranslationLoader(__DIR__ . '/Applications/Presentation/Assets/Locales'));
        $translator->addLoader(new XmlTranslationLoader(__DIR__ . '/Applications/Profiling/Assets/Locales'));
        $translator->addLoader(new XmlTranslationLoader(__DIR__ . '/Applications/Teller/Assets/Locales'));
        $translator->addLoader(new JsonTranslationLoader(__DIR__ . '/APIs/Assets/Exceptions'));
        $translator->addLoader(new JsonTranslationLoader(__DIR__ . '/Framework/CPF/Assets/Exceptions'));

        return $translator;
    });

    // View Services
    $builder->addScoped(View::class, function (Application $app) {
        $view = new View(__DIR__); // Base path for views
        // Use ServiceLocator here since View is created inside a closure
        $view->setComponentService(ServiceLocator::get(ComponentService::class));
        $view->setEscaper(ServiceLocator::get(Escaper::class));
        $view->setHelpersRegistry(ServiceLocator::get(HelpersRegistry::class));
        return $view;
    });
    $builder->addScoped(ComponentService::class, fn(Application $app) => new ComponentService(
        $app,
        'App\\ViewComponents' // Define your component namespace
    ));
    $builder->addScoped(Escaper::class, Escaper::class);
    $builder->addScoped(HelpersRegistry::class, HelpersRegistry::class);

    // === 5. Register All API & Application Services ===
    // -- Contexts
    $builder->addScoped(BillingContext::class, BillingContext::class);
    $builder->addScoped(DTOContext::class, DTOContext::class);
    $builder->addScoped(HrmContext::class, HrmContext::class);
    $builder->addScoped(InventoryContext::class, InventoryContext::class);
    $builder->addScoped(InvoicingContext::class, InvoicingContext::class);
    $builder->addScoped(PaymentsContext::class, PaymentsContext::class);
    $builder->addScoped(ProfilingContext::class, ProfilingContext::class);
    $builder->addScoped(PurchaseContext::class, PurchaseContext::class);
    $builder->addScoped(RelationContext::class, RelationContext::class);
    $builder->addScoped(TaxesContext::class, TaxesContext::class);
    $builder->addScoped(TellerContext::class, TellerContext::class);
    $builder->addScoped(TokenContext::class, TokenContext::class);

    // -- Repositories (Persistence)
    $builder->addScoped(CurrencyRepository::class, CurrencyRepository::class);
    $builder->addScoped(DiscountRepository::class, DiscountRepository::class);
    $builder->addScoped(PriceRepository::class, PriceRepository::class);
    $builder->addScoped(AppCategoryRepository::class, AppCategoryRepository::class);
    $builder->addScoped(AppRepository::class, AppRepository::class);
    $builder->addScoped(AuditRepository::class, AuditRepository::class);
    $builder->addScoped(CityRepository::class, CityRepository::class);
    $builder->addScoped(ContinentRepository::class, ContinentRepository::class);
    $builder->addScoped(CountryRepository::class, CountryRepository::class);
    $builder->addScoped(LanguageRepository::class, LanguageRepository::class);
    $builder->addScoped(ParameterRepository::class, ParameterRepository::class);
    $builder->addScoped(EmployeeRepository::class, EmployeeRepository::class);
    $builder->addScoped(PackagingRepository::class, PackagingRepository::class);
    $builder->addScoped(ProductCategoryRepository::class, ProductCategoryRepository::class);
    $builder->addScoped(ProductRepository::class, ProductRepository::class);
    $builder->addScoped(StockRepository::class, StockRepository::class);
    $builder->addScoped(UnitRepository::class, UnitRepository::class);
    $builder->addScoped(CustomerRepository::class, CustomerRepository::class);
    $builder->addScoped(PaymentMethodRepository::class, PaymentMethodRepository::class);
    $builder->addScoped(CivilityRepository::class, CivilityRepository::class);
    $builder->addScoped(ContactRepository::class, ContactRepository::class);
    $builder->addScoped(ContactTypeRepository::class, ContactTypeRepository::class);
    $builder->addScoped(CredentialRepository::class, CredentialRepository::class);
    $builder->addScoped(GenderRepository::class, GenderRepository::class);
    $builder->addScoped(OccupationRepository::class, OccupationRepository::class);
    $builder->addScoped(PermissionRepository::class, PermissionRepository::class);
    $builder->addScoped(ProfileRepository::class, ProfileRepository::class);
    $builder->addScoped(RoleRepository::class, RoleRepository::class);
    $builder->addScoped(StatusRepository::class, StatusRepository::class);
    $builder->addScoped(TitleRepository::class, TitleRepository::class);
    $builder->addScoped(TokenRepository::class, TokenRepository::class);
    $builder->addScoped(TrackingRepository::class, TrackingRepository::class);
    $builder->addScoped(SupplierRepository::class, SupplierRepository::class);
    $builder->addScoped(AppRelationRepository::class, AppRelationRepository::class);
    $builder->addScoped(CashRelationRepository::class, CashRelationRepository::class);
    $builder->addScoped(CivilityRelationRepository::class, CivilityRelationRepository::class);
    $builder->addScoped(ContactRelationRepository::class, ContactRelationRepository::class);
    $builder->addScoped(GenderRelationRepository::class, GenderRelationRepository::class);
    $builder->addScoped(LanguageRelationRepository::class, LanguageRelationRepository::class);
    $builder->addScoped(OccupationRelationRepository::class, OccupationRelationRepository::class);
    $builder->addScoped(ParameterRelationRepository::class, ParameterRelationRepository::class);
    $builder->addScoped(PriceRelationRepository::class, PriceRelationRepository::class);
    $builder->addScoped(RoleRelationRepository::class, RoleRelationRepository::class);
    $builder->addScoped(StatusRelationRepository::class, StatusRelationRepository::class);
    $builder->addScoped(TitleRelationRepository::class, TitleRelationRepository::class);
    $builder->addScoped(TaxRepository::class, TaxRepository::class);
    $builder->addScoped(CashFigureRepository::class, CashFigureRepository::class);
    $builder->addScoped(TellerAuditRepository::class, TellerAuditRepository::class);
    $builder->addScoped(TellerCashCountRepository::class, TellerCashCountRepository::class);
    $builder->addScoped(TellerPaymentRepository::class, TellerPaymentRepository::class);
    $builder->addScoped(TellerReceiptRepository::class, TellerReceiptRepository::class);
    $builder->addScoped(TellerRepository::class, TellerRepository::class);
    $builder->addScoped(TellerReversalRepository::class, TellerReversalRepository::class);
    $builder->addScoped(TellerSessionRepository::class, TellerSessionRepository::class);
    $builder->addScoped(TellerTransactionRepository::class, TellerTransactionRepository::class);
    $builder->addScoped(TellerTransferRepository::class, TellerTransferRepository::class);

    // -- Factories (Supporting)
    $builder->addScoped(PriceFactory::class, PriceFactory::class);
    $builder->addScoped(AppFactory::class, AppFactory::class);
    $builder->addScoped(CityFactory::class, CityFactory::class);
    $builder->addScoped(CollectableFactory::class, CollectableFactory::class);
    $builder->addScoped(CountryFactory::class, CountryFactory::class);
    $builder->addScoped(ParameterFactory::class, ParameterFactory::class);
    $builder->addScoped(EmployeeFactory::class, EmployeeFactory::class);
    $builder->addScoped(ProductFactory::class, ProductFactory::class);
    $builder->addScoped(StockFactory::class, StockFactory::class);
    $builder->addScoped(CustomerFactory::class, CustomerFactory::class);
    $builder->addScoped(CivilityFactory::class, CivilityFactory::class);
    $builder->addScoped(ContactFactory::class, ContactFactory::class);
    $builder->addScoped(CredentialFactory::class, CredentialFactory::class);
    $builder->addScoped(GenderFactory::class, GenderFactory::class);
    $builder->addScoped(OccupationFactory::class, OccupationFactory::class);
    $builder->addScoped(ProfileFactory::class, ProfileFactory::class);
    $builder->addScoped(RoleFactory::class, RoleFactory::class);
    $builder->addScoped(StatusFactory::class, StatusFactory::class);
    $builder->addScoped(TitleFactory::class, TitleFactory::class);
    $builder->addScoped(TokenFactory::class, TokenFactory::class);
    $builder->addScoped(TrackingFactory::class, TrackingFactory::class);
    $builder->addScoped(SupplierFactory::class, SupplierFactory::class);
    $builder->addScoped(TellerCashCountFactory::class, TellerCashCountFactory::class);
    $builder->addScoped(TellerFactory::class, TellerFactory::class);
    $builder->addScoped(TellerPaymentFactory::class, TellerPaymentFactory::class);
    $builder->addScoped(TellerReceiptFactory::class, TellerReceiptFactory::class);
    $builder->addScoped(TellerReversalFactory::class, TellerReversalFactory::class);
    $builder->addScoped(TellerSessionFactory::class, TellerSessionFactory::class);
    $builder->addScoped(TellerTransactionFactory::class, TellerTransactionFactory::class);
    $builder->addScoped(TellerTransferFactory::class, TellerTransferFactory::class);

    // -- Services (Contracts -> Implementations)
    $builder->addScoped(IAppCategoryService::class, AppCategoryService::class);
    $builder->addScoped(IAppService::class, AppService::class);
    $builder->addScoped(IAuditService::class, AuditService::class);
    $builder->addScoped(ICityService::class, CityService::class);
    $builder->addScoped(IContinentService::class, ContinentService::class);
    $builder->addScoped(ICountryService::class, CountryService::class);
    $builder->addScoped(ILanguageService::class, LanguageService::class);
    $builder->addScoped(IParameterService::class, ParameterService::class);
    $builder->addScoped(ICurrencyService::class, CurrencyService::class);
    $builder->addScoped(IDiscountService::class, DiscountService::class);
    $builder->addScoped(IPriceService::class, PriceService::class);
    $builder->addScoped(IEmployeeService::class, EmployeeService::class);
    $builder->addScoped(IPackagingService::class, PackagingService::class);
    $builder->addScoped(IProductService::class, ProductService::class);
    $builder->addScoped(IStockService::class, StockService::class);
    $builder->addScoped(IUnitService::class, UnitService::class);
    $builder->addScoped(ICustomerService::class, CustomerService::class);
    $builder->addScoped(IPaymentMethodService::class, PaymentMethodService::class);
    $builder->addScoped(IAuthenticationService::class, AuthenticationService::class);
    $builder->addScoped(ICivilityService::class, CivilityService::class);
    $builder->addScoped(IContactService::class, ContactService::class);
    $builder->addScoped(IContactTypeService::class, ContactTypeService::class);
    $builder->addScoped(ICredentialService::class, CredentialService::class);
    $builder->addScoped(IGenderService::class, GenderService::class);
    $builder->addScoped(IOccupationService::class, OccupationService::class);
    $builder->addScoped(IProfileService::class, ProfileService::class);
    $builder->addScoped(IStatusService::class, StatusService::class);
    $builder->addScoped(ITitleService::class, TitleService::class);
    $builder->addScoped(ISupplierService::class, SupplierService::class);
    $builder->addScoped(ITaxService::class, TaxService::class);
    $builder->addScoped(ICashFigureService::class, CashFigureService::class);
    $builder->addScoped(ITellerAuditService::class, TellerAuditService::class);
    $builder->addScoped(ITellerCashCountService::class, TellerCashCountService::class);
    $builder->addScoped(ITellerPaymentService::class, TellerPaymentService::class);
    $builder->addScoped(ITellerReceiptService::class, TellerReceiptService::class);
    $builder->addScoped(ITellerReversalService::class, TellerReversalService::class);
    $builder->addScoped(ITellerService::class, TellerService::class);
    $builder->addScoped(ITellerSessionService::class, TellerSessionService::class);
    $builder->addScoped(ITellerTransactionService::class, TellerTransactionService::class);
    $builder->addScoped(ITellerTransferService::class, TellerTransferService::class);

    // -- API Facades (Scoped)
    $builder->addScoped(AppFacade::class, AppFacade::class);
    $builder->addScoped(AuditFacade::class, AuditFacade::class);
    $builder->addScoped(CountryFacade::class, CountryFacade::class);
    $builder->addScoped(LanguageFacade::class, LanguageFacade::class);
    $builder->addScoped(CurrencyFacade::class, CurrencyFacade::class);
    $builder->addScoped(DiscountFacade::class, DiscountFacade::class);
    $builder->addScoped(PriceFacade::class, PriceFacade::class);
    $builder->addScoped(EmployeeFacade::class, EmployeeFacade::class);
    $builder->addScoped(ProductFacade::class, ProductFacade::class);
    $builder->addScoped(StockFacade::class, StockFacade::class);
    $builder->addScoped(CustomerFacade::class, CustomerFacade::class);
    $builder->addScoped(PaymentFacade::class, PaymentFacade::class);
    $builder->addScoped(ContactFacade::class, ContactFacade::class);
    $builder->addScoped(CredentialFacade::class, CredentialFacade::class);
    $builder->addScoped(ProfileFacade::class, ProfileFacade::class);
    $builder->addScoped(SupplierFacade::class, SupplierFacade::class);
    $builder->addScoped(TaxFacade::class, TaxFacade::class);
    $builder->addScoped(CashFigureFacade::class, CashFigureFacade::class);
    $builder->addScoped(TellerAuditFacade::class, TellerAuditFacade::class);
    $builder->addScoped(TellerFacade::class, TellerFacade::class);
    $builder->addScoped(TellerSessionFacade::class, TellerSessionFacade::class);

    // -- API Controllers (Scoped)
    $builder->addScoped(AppController::class, AppController::class);
    $builder->addScoped(AuditController::class, AuditController::class);
    $builder->addScoped(CountryController::class, CountryController::class);
    $builder->addScoped(LanguageController::class, LanguageController::class);
    $builder->addScoped(ParameterController::class, ParameterController::class);
    $builder->addScoped(CurrencyController::class, CurrencyController::class);
    $builder->addScoped(DiscountController::class, DiscountController::class);
    $builder->addScoped(PriceController::class, PriceController::class);
    $builder->addScoped(EmployeeController::class, EmployeeController::class);
    $builder->addScoped(ProductController::class, ProductController::class);
    $builder->addScoped(StockController::class, StockController::class);
    $builder->addScoped(CustomerController::class, CustomerController::class);
    $builder->addScoped(PaymentController::class, PaymentController::class);
    $builder->addScoped(AuthenticationController::class, AuthenticationController::class);
    $builder->addScoped(ContactController::class, ContactController::class);
    $builder->addScoped(CredentialController::class, CredentialController::class);
    $builder->addScoped(ProfileController::class, ProfileController::class);
    $builder->addScoped(SupplierController::class, SupplierController::class);
    $builder->addScoped(TaxController::class, TaxController::class);
    $builder->addScoped(CashFigureController::class, CashFigureController::class);
    $builder->addScoped(TellerAuditController::class, TellerAuditController::class);
    $builder->addScoped(TellerController::class, TellerController::class);
    $builder->addScoped(TellerSessionController::class, TellerSessionController::class);

    // === 6. Register All Application (Web) Controllers (Transient) ===
    $builder->addTransient(AdministrationController::class, AdministrationController::class);
    $builder->addTransient(PresentationHomeController::class, PresentationHomeController::class);

    // === 7. Build and return the Application ===
    return $builder->build();

} catch (CacheException $e) {
    // A failsafe if caching is not configured properly
    // In a real app, you might want to log this or die()
    echo "Error initializing cache: " . $e->getMessage();
    exit;
}
