<?php
/* Cashledger autoload */

spl_autoload_register(function ($class){
    // Define an associative array of namespace prefixes and their base directories
    $prefixes = [
        // Framework
        'TS_Configuration\\' => __DIR__.'/Framework/ConfigurationLibrary/',
        'TS_Controller\\' => __DIR__.'/Framework/ControllerLibrary/',
        'TS_Database\\' => __DIR__.'/Framework/DatabaseLibrary/',
        'TS_DependencyInjection\\' => __DIR__.'/Framework/DependencyLibrary/',
        'TS_Domain\\' => __DIR__.'/Framework/DomainLibrary/',
        'TS_Exception\\' => __DIR__.'/Framework/ExceptionLibrary/',
        'TS_Locale\\' => __DIR__.'/Framework/LocaleLibrary/',
        'TS_Utility\\' => __DIR__.'/Framework/UtilityLibrary/',
        // APIs
        //- Persistence
        //-- DTOs
        'API_DTORepositories_Collection\\' => __DIR__.'/APIs/Persistence/DTORepositories/Collections/',
        'API_DTORepositories_Context\\' => __DIR__.'/APIs/Persistence/DTORepositories/Contexts/',
        'API_DTORepositories_Contract\\' => __DIR__.'/APIs/Persistence/DTORepositories/Contracts/',
        'API_DTORepositories_Model\\' => __DIR__.'/APIs/Persistence/DTORepositories/Models/',
        'API_DTORepositories\\' => __DIR__.'/APIs/Persistence/DTORepositories/Repositories/',
        //-- Inventory
        'API_InventoryRepositories_Collection\\' => __DIR__.'/APIs/Persistence/InventoryRepositories/Collections/',
        'API_InventoryRepositories_Context\\' => __DIR__.'/APIs/Persistence/InventoryRepositories/Contexts/',
        'API_InventoryRepositories_Contract\\' => __DIR__.'/APIs/Persistence/InventoryRepositories/Contracts/',
        'API_InventoryRepositories_Model\\' => __DIR__.'/APIs/Persistence/InventoryRepositories/Models/',
        'API_InventoryRepositories\\' => __DIR__.'/APIs/Persistence/InventoryRepositories/Repositories/',
        //-- Profiling
        'API_ProfilingRepositories_Collection\\' => __DIR__.'/APIs/Persistence/ProfilingRepositories/Collections/',
        'API_ProfilingRepositories_Context\\' => __DIR__.'/APIs/Persistence/ProfilingRepositories/Contexts/',
        'API_ProfilingRepositories_Contract\\' => __DIR__.'/APIs/Persistence/ProfilingRepositories/Contracts/',
        'API_ProfilingRepositories_Model\\' => __DIR__.'/APIs/Persistence/ProfilingRepositories/Models/',
        'API_ProfilingRepositories\\' => __DIR__.'/APIs/Persistence/ProfilingRepositories/Repositories/',
        //-- Relations
        'API_RelationRepositories_Collection\\' => __DIR__.'/APIs/Persistence/RelationRepositories/Collections/',
        'API_RelationRepositories_Context\\' => __DIR__.'/APIs/Persistence/RelationRepositories/Contexts/',
        'API_RelationRepositories_Contract\\' => __DIR__.'/APIs/Persistence/RelationRepositories/Contracts/',
        'API_RelationRepositories_Model\\' => __DIR__.'/APIs/Persistence/RelationRepositories/Models/',
        'API_RelationRepositories\\' => __DIR__.'/APIs/Persistence/RelationRepositories/Repositories/',
        // Supporting
        //- DTOs
        'API_DTOEntities_Collection\\' => __DIR__.'/APIs/Supporting/DTOEntities/Collections/',
        'API_DTOEntities_Contract\\' => __DIR__.'/APIs/Supporting/DTOEntities/Contracts/',
        'API_DTOEntities_Factory\\' => __DIR__.'/APIs/Supporting/DTOEntities/Factories/',
        'API_DTOEntities_Model\\' => __DIR__.'/APIs/Supporting/DTOEntities/Models/',
        //- Profiling
        'API_ProfilingEntities_Collection\\' => __DIR__.'/APIs/Supporting/ProfilingEntities/Collections/',
        'API_ProfilingEntities_Contract\\' => __DIR__.'/APIs/Supporting/ProfilingEntities/Contracts/',
        'API_ProfilingEntities_Factory\\' => __DIR__.'/APIs/Supporting/ProfilingEntities/Factories/',
        'API_ProfilingEntities_Model\\' => __DIR__.'/APIs/Supporting/ProfilingEntities/Models/',
        //- Inventory
        'API_InventoryEntities_Collection\\' => __DIR__.'/APIs/Supporting/InventoryEntities/Collections/',
        'API_InventoryEntities_Contract\\' => __DIR__.'/APIs/Supporting/InventoryEntities/Contracts/',
        'API_InventoryEntities_Factory\\' => __DIR__.'/APIs/Supporting/InventoryEntities/Factories/',
        'API_InventoryEntities_Model\\' => __DIR__.'/APIs/Supporting/InventoryEntities/Models/',
        // Services
        //- Administration
        'API_Administration_Contract\\' => __DIR__.'/APIs/Services/Administration/Contracts/',
        'API_Administration_Controller\\' => __DIR__.'/APIs/Services/Administration/Controllers/',
        'API_Administration_Service\\' => __DIR__.'/APIs/Services/Administration/Services/',
        //- Profiling
        'API_Profiling_Contract\\' => __DIR__.'/APIs/Services/Profiling/Contracts/',
        'API_Profiling_Controller\\' => __DIR__.'/APIs/Services/Profiling/Controllers/',
        'API_Profiling_Service\\' => __DIR__.'/APIs/Services/Profiling/Services/',
        //- Inventory
        'API_Inventory_Contract\\' => __DIR__.'/APIs/Services/Inventory/Contracts/',
        'API_Inventory_Controller\\' => __DIR__.'/APIs/Services/Inventory/Controllers/',
        'API_Inventory_Service\\' => __DIR__.'/APIs/Services/Inventory/Services/',
        // Applications
        //- Accounting
        'APP_Accounting_Controller\\' => __DIR__.'/Applications/Accounting/Controllers/',
        //- Administration
        'APP_Administration_Controller\\' => __DIR__.'/Applications/Administration/Controllers/',
        //- Billing
        'APP_Billing_Controller\\' => __DIR__.'/Applications/Billing/Controllers/',
        //- Booking
        'APP_Booking_Controller\\' => __DIR__.'/Applications/Booking/Controllers/',
        //- Control
        'APP_Control_Controller\\' => __DIR__.'/Applications/Control/Controllers/',
        //- Dashboarding
        'APP_Dashboarding_Controller\\' => __DIR__.'/Applications/Dashboarding/Controllers/',
        //- Emailing
        'APP_Emailing_Controller\\' => __DIR__.'/Applications/Emailing/Controllers/',
        //- Forecasting
        'APP_Forecasting_Controller\\' => __DIR__.'/Applications/Forecasting/Controllers/',
        //- HRM
        'APP_HRM_Controller\\' => __DIR__.'/Applications/Hrm/Controllers/',
        //- Hudel
        'APP_Hudel_Controller\\' => __DIR__.'/Applications/Hudel/Controllers/',
        //- IDS
        'APP_IDS_Controller\\' => __DIR__.'/Applications/Ids/Controllers/',
        //- Inventory
        'APP_Inventory_Controller\\' => __DIR__.'/Applications/Inventory/Controllers/',
        'APP_Inventory_Model\\' => __DIR__.'/Applications/Inventory/Models/',
        //- Invoicing
        'APP_Invoicing_Controller\\' => __DIR__.'/Applications/Invoicing/Controllers/',
        //- Meeting
        'APP_Meeting_Controller\\' => __DIR__.'/Applications/Meeting/Controllers/',
        //- Messaging
        'APP_Messaging_Controller\\' => __DIR__.'/Applications/Messaging/Controllers/',
        //- Partnership
        'APP_Partnership_Controller\\' => __DIR__.'/Applications/Partnership/Controllers/',
        //- Payments
        'APP_Payments_Controller\\' => __DIR__.'/Applications/Payments/Controllers/',
        //- Payroll
        'APP_Payroll_Controller\\' => __DIR__.'/Applications/Payroll/Controllers/',
        //- Presentation
        'APP_Presentation_Controller\\' => __DIR__.'/Applications/Presentation/Controllers/',
        'APP_Presentation_Model\\' => __DIR__.'/Applications/Presentation/Models/',
        //- Profiling
        'APP_Profiling_Controller\\' => __DIR__.'/Applications/Profiling/Controllers/',
        //- Publishing
        'APP_Publishing_Controller\\' => __DIR__.'/Applications/Publishing/Controllers/',
        //- Purchase
        'APP_Purchase_Controller\\' => __DIR__.'/Applications/Purchase/Controllers/',
        //- Reporting
        'APP_Reporting_Controller\\' => __DIR__.'/Applications/Reporting/Controllers/',
        //- Tasks
        'APP_Tasks_Controller\\' => __DIR__.'/Applications/Tasks/Controllers/',
        //- Teller
        'APP_Teller_Controller\\' => __DIR__.'/Applications/Teller/Controllers/',
        //- Wholesale
        'APP_Wholesale_Controller\\' => __DIR__.'/Applications/Wholesale/Controllers/'
    ];

    foreach ($prefixes as $prefix => $path) {
        // Check if the class uses the namespace prefix
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0)
            break;
    }

    // Get the relative class name
    $relative_class = substr($class, $len);

    // Replace namespace separators with directory separators, append with .php
    $file = $path.str_replace('\\', '/', $relative_class).'.php';

    // If file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});