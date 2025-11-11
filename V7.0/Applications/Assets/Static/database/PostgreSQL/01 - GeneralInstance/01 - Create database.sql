-- Database: Cashledger

--DROP DATABASE IF EXISTS "Cashledger";

CREATE DATABASE "Cashledger"
    WITH
    OWNER = postgres
	TEMPLATE = Template0
    ENCODING = 'UTF8'
    LC_COLLATE = 'English_United States.1252'
    LC_CTYPE = 'English_United States.1252'
    ICU_LOCALE = 'fr-FR'
    LOCALE_PROVIDER = 'icu'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1
    IS_TEMPLATE = False;

/*
 * Code (3 digits)

 Accounting app
 
 Administration app
 * cl_Parameters
 * cl_ParameterRelations
 * ACT cl_AppCategories
 * APP cl_Apps
 * APR cl_AppRelations
 * AUD cl_Audits
 * CIT cl_Cities
 * CTN cl_Continents
 * CTY cl_Countries
 * LNG cl_Languages
 * LGR cl_LanguageRelations
 * RFT cl_ReferenceTables
 * RFR cl_ReferenceRelations

 Billing app
 * CUR cl_Currencies
 * DCT cl_Discounts
 * PRC cl_Prices
 * PCR cl_PriceRelations
 
 Booking app
 Control app
 Corporation app
 Dashboarding app
 Emailing app
 Forecasting app
 
 Hrm app
 * EMP cl_Employees
 
 Hudel app
 Ids app
 
 Inventory app
 * ATR cl_AttributeRelations
 * DLN cl_DeliveryNotes
 * DLR cl_DeliveryRelations
 * DPN cl_DispatchNotes
 * DPR cl_DispatchRelations
 * INN cl_InventNotes
 * INR cl_InventRelations
 * IVT cl_Inventories
 * IVR cl_InventoryRelations
 * MFR cl_Manufacturers
 * PKG cl_Packagings
 * PRA cl_ProductAttributes
 * PRD cl_ProductCategories
 * PRT cl_Products
 * RTN cl_ReturnNotes
 * RTR cl_ReturnRelations
 * SKR cl_StockRelations
 * STK cl_Stocks
 * TRN cl_TransferNotes
 * TRR cl_TransferRelations
 * UNT cl_Units
 * WRH cl_Warehouses
 * WSN cl_WasteNotes
 * WSR cl_WasteRelations
 
 Invoicing app
 * CUS cl_Customers
 * INS cl_InvoiceStatuses
 * INV cl_Invoices
 * INR cl_InvoiceRelations
 
 Maintenance app
 Meeting app
 Messaging app
 Partnership app
 
 Payments app
 * PAY cl_Payments
 * PYM cl_PaymentMethods
 
 Payroll app

 Profiling app
 * CIV cl_Civilities
 * CTC cl_Contacts
 * CTR cl_ContactRelations
 * CTT cl_ContactTypes
 * CVR cl_CivilityRelations
 * CRD cl_Credentials
 * GND cl_Genders
 * GNR cl_GenderRelations
 * OCP cl_Occupations
 * OCR cl_OccupationRelations
 * PRL cl_Profiles
 * PRM cl_Permissions
 * PRR cl_Role_*
 * ROL cl_Roles
 * ROR cl_RoleRelations
 * STS cl_Statuses
 * STR cl_StatusRelations
 * TRK cl_Trackings
 * TTL cl_Titles
 * TTR cl_TitleRelations

 Publishing app
 
 Purchase app
 * SUP cl_Suppliers
 
 Reporting app
 Tasks app
 
 Taxes app
 * PXR cl_PartnerTaxRelations
 * TAX cl_Taxes
 * TXA cl_TaxAttributes
 * TXP cl_TaxProfiles
 * TXR cl_TaxRelations
 * TXT cl_TaxTypes

 Teller app
 * CFG cl_CashFigures
 * CSR cl_CashRelations
 * TAU cl_TellerAudits
 * TCC cl_TellerCashCounts
 * TEL cl_Tellers
 * TLT cl_TellerTransfers
 * TPM cl_TellerPayments
 * TRT cl_TellerReceipts
 * TSS cl_TellerSessions
 * TTN cl_TellerTransactions
 * TVS cl_TellerReversals
 
 Wholesale app

 Summary
 * ACT cl_AppCategories
 * APP cl_Apps
 * APR cl_AppRelations
 * ATR cl_AttributeRelations
 * AUD cl_Audits
 * CFG cl_CashFigures
 * CIT cl_Cities
 * CIV cl_Civilities
 * CRD cl_Credentials
 * CSR cl_CashRelations
 
 * CTC cl_Contacts
 * CTN cl_Continents
 * CTR cl_ContactRelations
 * CTT cl_ContactTypes
 * CTY cl_Countries
 * CUR cl_Currencies
 * CUS cl_Customers
 * CVR cl_CivilityRelations
 * DCT cl_Discounts
 * DLN cl_DeliveryNotes
 
 * DLR cl_DeliveryRelations
 * DPN cl_DispatchNotes
 * DPR cl_DispatchRelations
 * EMP cl_Employees
 * GND cl_Genders
 * GNR cl_GenderRelations
 * INR cl_InvoiceRelations
 * INS cl_InvoiceStatuses
 * IVN cl_InventNotes
 * IVR cl_InventRelations
 
 * IVT cl_Inventories
 * IVR cl_InventoryRelations
 * LGR cl_LanguageRelations
 * LNG cl_Languages
 * MFR cl_Manufacturers
 * OCP cl_Occupations
 * OCR cl_OccupationRelations
 * PAY cl_Payments
 * PCR cl_PriceRelations
 * PKG cl_Packagings
 
 * PRA cl_ProductAttributes
 * PRC cl_Prices
 * PRD cl_ProductCategories
 * PRL cl_Profiles
 * PRM cl_Permissions
 * PRR cl_Role_*
 * PRT cl_Products
 * PXR cl_PartnerTaxRelations
 * PYM cl_PaymentMethods
 * RFR cl_ReferenceRelations
 
 * RFT cl_ReferenceTables
 * ROL cl_Roles
 * ROR cl_RoleRelations
 * RTN cl_ReturnNotes
 * RTR cl_ReturnRelations
 * SKR cl_StockRelations
 * STK cl_Stocks
 * STR cl_StatusRelations
 * STS cl_Status
 * SUP cl_Suppliers
 
 * TAU cl_TellerAudits
 * TAX cl_Taxes
 * TCC cl_TellerCashCounts
 * TEL cl_Tellers
 * TLT cl_TellerTransfers
 * TPM cl_TellerPayments
 * TRK cl_Trackings
 * TRN cl_TransferNotes
 * TRR cl_TransferRelations
 * TRT cl_TellerReceipts
 
 * TSS cl_TellerSessions
 * TTL cl_Titles
 * TTN cl_TellerTransactions
 * TTR cl_TitleRelations
 * TVS cl_TellerReversals
 * TXA cl_TaxAttributes
 * TXP cl_TaxProfiles
 * TXR cl_TaxRelations
 * TXT cl_TaxTypes
 * UNT cl_Units
 
 * WRH cl_Warehouses
 * WSN cl_WasteNotes
 * WSR cl_WasteRelations
 */
 