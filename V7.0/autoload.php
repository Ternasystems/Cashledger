<?php
/* Cashledger autoload */

spl_autoload_register(function ($class){
    // Define an associative array of namespace prefixes and their base directories
    $prefixes = [
        // Framework
        'TS_Cache\\' => __DIR__.'/Framework/CPF/CacheLibrary',
        'TS_Configuration\\' => __DIR__.'/Framework/CPF/ConfigurationLibrary/',
        'TS_Controller\\' => __DIR__.'/Framework/CPF/ControllerLibrary/',
        'TS_Database\\' => __DIR__.'/Framework/CPF/DatabaseLibrary/',
        'TS_DependencyInjection\\' => __DIR__.'/Framework/CPF/DependencyLibrary/',
        'TS_Domain\\' => __DIR__.'/Framework/CPF/DomainLibrary/',
        'TS_Exception\\' => __DIR__.'/Framework/CPF/ExceptionLibrary/',
        'TS_Http\\' => __DIR__.'/Framework/CPF/HttpLibrary/',
        'TS_Locale\\' => __DIR__.'/Framework/CPF/LocaleLibrary/',
        'TS_Utility\\' => __DIR__.'/Framework/CPF/UtilityLibrary/',
        'TS_View\\' => __DIR__.'/Framework/CPF/ViewLibrary/',

        // APIs
        // -Persistence
        // --Billing
        'API_BillingRepositories_Collection\\' => __DIR__.'/APIs/Persistence/BillingRepositories/Collections/',
        'API_BillingRepositories_Context\\' => __DIR__.'/APIs/Persistence/BillingRepositories/Contexts/',
        'API_BillingRepositories_Contract\\' => __DIR__.'/APIs/Persistence/BillingRepositories/Contracts/',
        'API_BillingRepositories_Model\\' => __DIR__.'/APIs/Persistence/BillingRepositories/Models/',
        'API_BillingRepositories\\' => __DIR__.'/APIs/Persistence/BillingRepositories/Repositories/',
        // --DTO
        'API_DTORepositories_Collection\\' => __DIR__.'/APIs/Persistence/DTORepositories/Collections/',
        'API_DTORepositories_Context\\' => __DIR__.'/APIs/Persistence/DTORepositories/Contexts/',
        'API_DTORepositories_Contract\\' => __DIR__.'/APIs/Persistence/DTORepositories/Contracts/',
        'API_DTORepositories_Model\\' => __DIR__.'/APIs/Persistence/DTORepositories/Models/',
        'API_DTORepositories\\' => __DIR__.'/APIs/Persistence/DTORepositories/Repositories/',
        // --Hrm
        'API_HrmRepositories_Collection\\' => __DIR__.'/APIs/Persistence/HrmRepositories/Collections/',
        'API_HrmRepositories_Context\\' => __DIR__.'/APIs/Persistence/HrmRepositories/Contexts/',
        'API_HrmRepositories_Contract\\' => __DIR__.'/APIs/Persistence/HrmRepositories/Contracts/',
        'API_HrmRepositories_Model\\' => __DIR__.'/APIs/Persistence/HrmRepositories/Models/',
        'API_HrmRepositories\\' => __DIR__.'/APIs/Persistence/HrmRepositories/Repositories/',
        // --Inventory
        'API_InventoryRepositories_Collection\\' => __DIR__.'/APIs/Persistence/InventoryRepositories/Collections/',
        'API_InventoryRepositories_Context\\' => __DIR__.'/APIs/Persistence/InventoryRepositories/Contexts/',
        'API_InventoryRepositories_Contract\\' => __DIR__.'/APIs/Persistence/InventoryRepositories/Contracts/',
        'API_InventoryRepositories_Model\\' => __DIR__.'/APIs/Persistence/InventoryRepositories/Models/',
        'API_InventoryRepositories\\' => __DIR__.'/APIs/Persistence/InventoryRepositories/Repositories/',
        // --Invoicing
        'API_InvoicingRepositories_Collection\\' => __DIR__.'/APIs/Persistence/InvoicingRepositories/Collections/',
        'API_InvoicingRepositories_Context\\' => __DIR__.'/APIs/Persistence/InvoicingRepositories/Contexts/',
        'API_InvoicingRepositories_Contract\\' => __DIR__.'/APIs/Persistence/InvoicingRepositories/Contracts/',
        'API_InvoicingRepositories_Model\\' => __DIR__.'/APIs/Persistence/InvoicingRepositories/Models/',
        'API_InvoicingRepositories\\' => __DIR__.'/APIs/Persistence/InvoicingRepositories/Repositories/',
        // --Payments
        'API_PaymentsRepositories_Collection\\' => __DIR__.'/APIs/Persistence/PaymentsRepositories/Collections/',
        'API_PaymentsRepositories_Context\\' => __DIR__.'/APIs/Persistence/PaymentsRepositories/Contexts/',
        'API_PaymentsRepositories_Contract\\' => __DIR__.'/APIs/Persistence/PaymentsRepositories/Contracts/',
        'API_PaymentsRepositories_Model\\' => __DIR__.'/APIs/Persistence/PaymentsRepositories/Models/',
        'API_PaymentsRepositories\\' => __DIR__.'/APIs/Persistence/PaymentsRepositories/Repositories/',
        // --Profiling
        'API_ProfilingRepositories_Collection\\' => __DIR__.'/APIs/Persistence/ProfilingRepositories/Collections/',
        'API_ProfilingRepositories_Context\\' => __DIR__.'/APIs/Persistence/ProfilingRepositories/Contexts/',
        'API_ProfilingRepositories_Contract\\' => __DIR__.'/APIs/Persistence/ProfilingRepositories/Contracts/',
        'API_ProfilingRepositories_Model\\' => __DIR__.'/APIs/Persistence/ProfilingRepositories/Models/',
        'API_ProfilingRepositories\\' => __DIR__.'/APIs/Persistence/ProfilingRepositories/Repositories/',
        // --Purchase
        'API_PurchaseRepositories_Collection\\' => __DIR__.'/APIs/Persistence/PurchaseRepositories/Collections/',
        'API_PurchaseRepositories_Context\\' => __DIR__.'/APIs/Persistence/PurchaseRepositories/Contexts/',
        'API_PurchaseRepositories_Contract\\' => __DIR__.'/APIs/Persistence/PurchaseRepositories/Contracts/',
        'API_PurchaseRepositories_Model\\' => __DIR__.'/APIs/Persistence/PurchaseRepositories/Models/',
        'API_PurchaseRepositories\\' => __DIR__.'/APIs/Persistence/PurchaseRepositories/Repositories/',
        // --Relations
        'API_RelationRepositories_Collection\\' => __DIR__.'/APIs/Persistence/RelationRepositories/Collections/',
        'API_RelationRepositories_Context\\' => __DIR__.'/APIs/Persistence/RelationRepositories/Contexts/',
        'API_RelationRepositories_Contract\\' => __DIR__.'/APIs/Persistence/RelationRepositories/Contracts/',
        'API_RelationRepositories_Model\\' => __DIR__.'/APIs/Persistence/RelationRepositories/Models/',
        'API_RelationRepositories\\' => __DIR__.'/APIs/Persistence/RelationRepositories/Repositories/',
        // --Taxes
        'API_TaxesRepositories_Collection\\' => __DIR__.'/APIs/Persistence/TaxesRepositories/Collections/',
        'API_TaxesRepositories_Context\\' => __DIR__.'/APIs/Persistence/TaxesRepositories/Contexts/',
        'API_TaxesRepositories_Contract\\' => __DIR__.'/APIs/Persistence/TaxesRepositories/Contracts/',
        'API_TaxesRepositories_Model\\' => __DIR__.'/APIs/Persistence/TaxesRepositories/Models/',
        'API_TaxesRepositories\\' => __DIR__.'/APIs/Persistence/TaxesRepositories/Repositories/',
        // --Teller
        'API_TellerRepositories_Collection\\' => __DIR__.'/APIs/Persistence/TellerRepositories/Collections/',
        'API_TellerRepositories_Context\\' => __DIR__.'/APIs/Persistence/TellerRepositories/Contexts/',
        'API_TellerRepositories_Contract\\' => __DIR__.'/APIs/Persistence/TellerRepositories/Contracts/',
        'API_TellerRepositories_Model\\' => __DIR__.'/APIs/Persistence/TellerRepositories/Models/',
        'API_TellerRepositories\\' => __DIR__.'/APIs/Persistence/TellerRepositories/Repositories/',
        // -Supporting
        // --Billing
        'API_BillingEntities_Collection\\' => __DIR__.'/APIs/Supporting/BillingEntities/Collections/',
        'API_BillingEntities_Contract\\' => __DIR__.'/APIs/Supporting/BillingEntities/Contracts/',
        'API_BillingEntities_Factory\\' => __DIR__.'/APIs/Supporting/BillingEntities/Factories/',
        'API_BillingEntities_Model\\' => __DIR__.'/APIs/Supporting/BillingEntities/Models/',
        // --DTO
        'API_DTOEntities_Collection\\' => __DIR__.'/APIs/Supporting/DTOEntities/Collections/',
        'API_DTOEntities_Contract\\' => __DIR__.'/APIs/Supporting/DTOEntities/Contracts/',
        'API_DTOEntities_Factory\\' => __DIR__.'/APIs/Supporting/DTOEntities/Factories/',
        'API_DTOEntities_Model\\' => __DIR__.'/APIs/Supporting/DTOEntities/Models/',
        // --Hrm
        'API_HrmEntities_Collection\\' => __DIR__.'/APIs/Supporting/HrmEntities/Collections/',
        'API_HrmEntities_Contract\\' => __DIR__.'/APIs/Supporting/HrmEntities/Contracts/',
        'API_HrmEntities_Factory\\' => __DIR__.'/APIs/Supporting/HrmEntities/Factories/',
        'API_HrmEntities_Model\\' => __DIR__.'/APIs/Supporting/HrmEntities/Models/',
        // --Inventory
        'API_InventoryEntities_Collection\\' => __DIR__.'/APIs/Supporting/InventoryEntities/Collections/',
        'API_InventoryEntities_Contract\\' => __DIR__.'/APIs/Supporting/InventoryEntities/Contracts/',
        'API_InventoryEntities_Factory\\' => __DIR__.'/APIs/Supporting/InventoryEntities/Factories/',
        'API_InventoryEntities_Model\\' => __DIR__.'/APIs/Supporting/InventoryEntities/Models/',
        // --Invoicing
        'API_InvoicingEntities_Collection\\' => __DIR__.'/APIs/Supporting/InvoicingEntities/Collections/',
        'API_InvoicingEntities_Contract\\' => __DIR__.'/APIs/Supporting/InvoicingEntities/Contracts/',
        'API_InvoicingEntities_Factory\\' => __DIR__.'/APIs/Supporting/InvoicingEntities/Factories/',
        'API_InvoicingEntities_Model\\' => __DIR__.'/APIs/Supporting/InvoicingEntities/Models/',
        // --Payments
        'API_PaymentsEntities_Collection\\' => __DIR__.'/APIs/Supporting/PaymentsEntities/Collections/',
        'API_PaymentsEntities_Contract\\' => __DIR__.'/APIs/Supporting/PaymentsEntities/Contracts/',
        'API_PaymentsEntities_Factory\\' => __DIR__.'/APIs/Supporting/PaymentsEntities/Factories/',
        'API_PaymentsEntities_Model\\' => __DIR__.'/APIs/Supporting/PaymentsEntities/Models/',
        // --Profiling
        'API_ProfilingEntities_Collection\\' => __DIR__.'/APIs/Supporting/ProfilingEntities/Collections/',
        'API_ProfilingEntities_Contract\\' => __DIR__.'/APIs/Supporting/ProfilingEntities/Contracts/',
        'API_ProfilingEntities_Factory\\' => __DIR__.'/APIs/Supporting/ProfilingEntities/Factories/',
        'API_ProfilingEntities_Model\\' => __DIR__.'/APIs/Supporting/ProfilingEntities/Models/',
        // --Purchase
        'API_PurchaseEntities_Collection\\' => __DIR__.'/APIs/Supporting/PurchaseEntities/Collections/',
        'API_PurchaseEntities_Contract\\' => __DIR__.'/APIs/Supporting/PurchaseEntities/Contracts/',
        'API_PurchaseEntities_Factory\\' => __DIR__.'/APIs/Supporting/PurchaseEntities/Factories/',
        'API_PurchaseEntities_Model\\' => __DIR__.'/APIs/Supporting/PurchaseEntities/Models/',
        // --Taxes
        'API_TaxesEntities_Collection\\' => __DIR__.'/APIs/Supporting/TaxesEntities/Collections/',
        'API_TaxesEntities_Contract\\' => __DIR__.'/APIs/Supporting/TaxesEntities/Contracts/',
        'API_TaxesEntities_Factory\\' => __DIR__.'/APIs/Supporting/TaxesEntities/Factories/',
        'API_TaxesEntities_Model\\' => __DIR__.'/APIs/Supporting/TaxesEntities/Models/',
        // --Teller
        'API_TellerEntities_Collection\\' => __DIR__.'/APIs/Supporting/TellerEntities/Collections/',
        'API_TellerEntities_Contract\\' => __DIR__.'/APIs/Supporting/TellerEntities/Contracts/',
        'API_TellerEntities_Factory\\' => __DIR__.'/APIs/Supporting/TellerEntities/Factories/',
        'API_TellerEntities_Model\\' => __DIR__.'/APIs/Supporting/TellerEntities/Models/',
        // -Services
        // --Administration
        'API_Administration_Contract\\' => __DIR__.'/APIs/Services/Administration/Contracts/',
        'API_Administration_Controller\\' => __DIR__.'/APIs/Services/Administration/Controllers/',
        'API_Administration_Facade\\' => __DIR__.'/APIs/Services/Administration/Facades/',
        'API_Administration_Service\\' => __DIR__.'/APIs/Services/Administration/Services/',
        // --Billing
        'API_Billing_Contract\\' => __DIR__.'/APIs/Services/Billing/Contracts/',
        'API_Billing_Controller\\' => __DIR__.'/APIs/Services/Billing/Controllers/',
        'API_Billing_Facade\\' => __DIR__.'/APIs/Services/Billing/Facades/',
        'API_Billing_Service\\' => __DIR__.'/APIs/Services/Billing/Services/',
        // --Hrm
        'API_Hrm_Contract\\' => __DIR__.'/APIs/Services/Hrm/Contracts/',
        'API_Hrm_Controller\\' => __DIR__.'/APIs/Services/Hrm/Controllers/',
        'API_Hrm_Facade\\' => __DIR__.'/APIs/Services/Hrm/Facades/',
        'API_Hrm_Service\\' => __DIR__.'/APIs/Services/Hrm/Services/',
        // --Inventory
        'API_Inventory_Contract\\' => __DIR__.'/APIs/Services/Inventory/Contracts/',
        'API_Inventory_Controller\\' => __DIR__.'/APIs/Services/Inventory/Controllers/',
        'API_Inventory_Facade\\' => __DIR__.'/APIs/Services/Inventory/Facades/',
        'API_Inventory_Service\\' => __DIR__.'/APIs/Services/Inventory/Services/',
        // --Invoicing
        'API_Invoicing_Contract\\' => __DIR__.'/APIs/Services/Invoicing/Contracts/',
        'API_Invoicing_Controller\\' => __DIR__.'/APIs/Services/Invoicing/Controllers/',
        'API_Invoicing_Facade\\' => __DIR__.'/APIs/Services/Invoicing/Facades/',
        'API_Invoicing_Service\\' => __DIR__.'/APIs/Services/Invoicing/Services/',
        // --Payments
        'API_Payments_Contract\\' => __DIR__.'/APIs/Services/Payments/Contracts/',
        'API_Payments_Controller\\' => __DIR__.'/APIs/Services/Payments/Controllers/',
        'API_Payments_Facade\\' => __DIR__.'/APIs/Services/Payments/Facades/',
        'API_Payments_Service\\' => __DIR__.'/APIs/Services/Payments/Services/',
        // --Profiling
        'API_Profiling_Contract\\' => __DIR__.'/APIs/Services/Profiling/Contracts/',
        'API_Profiling_Controller\\' => __DIR__.'/APIs/Services/Profiling/Controllers/',
        'API_Profiling_Facade\\' => __DIR__.'/APIs/Services/Profiling/Facades/',
        'API_Profiling_Service\\' => __DIR__.'/APIs/Services/Profiling/Services/',
        // --Purchase
        'API_Purchase_Contract\\' => __DIR__.'/APIs/Services/Purchase/Contracts/',
        'API_Purchase_Controller\\' => __DIR__.'/APIs/Services/Purchase/Controllers/',
        'API_Purchase_Facade\\' => __DIR__.'/APIs/Services/Purchase/Facades/',
        'API_Purchase_Service\\' => __DIR__.'/APIs/Services/Purchase/Services/',
        // --Taxes
        'API_Taxes_Contract\\' => __DIR__.'/APIs/Services/Taxes/Contracts/',
        'API_Taxes_Controller\\' => __DIR__.'/APIs/Services/Taxes/Controllers/',
        'API_Taxes_Facade\\' => __DIR__.'/APIs/Services/Taxes/Facades/',
        'API_Taxes_Service\\' => __DIR__.'/APIs/Services/Taxes/Services/',
        // --Teller
        'API_Teller_Contract\\' => __DIR__.'/APIs/Services/Teller/Contracts/',
        'API_Teller_Controller\\' => __DIR__.'/APIs/Services/Teller/Controllers/',
        'API_Teller_Facade\\' => __DIR__.'/APIs/Services/Teller/Facades/',
        'API_Teller_Service\\' => __DIR__.'/APIs/Services/Teller/Services/',

        // Applications
        // *** UPDATED ***
        // We can use a single, simpler prefix for all apps
        'App\\' => __DIR__.'/Applications/',

        // --- (Keep your old prefixes as fallbacks if you like) ---
        'APP_Presentation_Controller\\' => __DIR__.'/Applications/Presentation/Controllers/',
        'APP_Presentation_Model\\' => __DIR__.'/Applications/Presentation/Models/',
        'APP_Profiling_Controller\\' => __DIR__.'/Applications/Profiling/Controllers/',
        // ... (etc) ...
    ];

    // Try the new 'App\\' prefix first
    $appPrefix = 'App\\';
    $appLen = strlen($appPrefix);
    if (strncmp($appPrefix, $class, $appLen) === 0) {
        $relative_class = substr($class, $appLen);
        $file = __DIR__ . '/Applications/' . str_replace('\\', '/', $relative_class) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }

    // Fallback to old prefixes
    $prefix_found = null;
    foreach ($prefixes as $prefix => $path) {
        // Check if the class uses the namespace prefix
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) === 0) {
            $prefix_found = $prefix;
            break;
        }
    }

    // If no prefix found, do nothing
    if ($prefix_found === null) {
        return;
    }

    // Get the relative class name
    $relative_class = substr($class, strlen($prefix_found));

    // Replace namespace separators with directory separators, append with .php
    $file = $prefixes[$prefix_found] . str_replace('\\', '/', $relative_class) . '.php';

    // If file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});