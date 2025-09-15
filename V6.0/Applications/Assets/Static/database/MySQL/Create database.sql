-- Database: Cashledger

CREATE DATABASE IF NOT EXISTS `Cashledger` CHARACTER SET = 'utf8mb4' COLLATE = 'utf8mb4_unicode_ci';

/*
 * Code (3 digits)

 Accounting app
 
 Administration app
 * ACT cl_AppCategories
 * APP cl_Apps
 * APR cl_AppRelations
 * AUD cl_Audits
 * CIT cl_Cities
 * CTN cl_Continents
 * CTY cl_Countries
 * LNG cl_Languages
 * LGR cl_LanguageRelations

 Billing app
 Booking app
 Control app
 Dashboarding app
 Emailing app
 Forecasting app
 Hrm app
 Hudel app
 Ids app
 
 Inventory app
 * ATR cl_AttributeRelations
 * CUS cl_Customers
 * DLN cl_DeliveryNotes
 * DLR cl_DeliveryRelations
 * DPN cl_DispatchNotes
 * DPR cl_DispatchRelations
 * GLN cl_Galenics
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
 * SUP cl_Suppliers
 * TRN cl_TransferNotes
 * TRR cl_TransferRelations
 * UNT cl_Units
 * WRH cl_Warehouses
 * WSN cl_WasteNotes
 * WSR cl_WasteRelations
 
 Invoicing app
 Meeting app
 Messaging app
 Partnership app
 Payments app
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
 Reporting app
 Tasks app
 Teller app
 Wholesale app

 Summary
 * ACT cl_AppCategories
 * APP cl_Apps
 * APR cl_AppRelations
 * ATR cl_AttributeRelations
 * AUD cl_Audits
 
 * CIT cl_Cities
 * CIV cl_Civilities
 * CRD cl_Credentials
 * CTC cl_Contacts
 * CTN cl_Continents
 
 * CTR cl_ContactRelations
 * CTT cl_ContactTypes
 * CTY cl_Countries
 * CUS cl_Customers
 * CVR cl_CivilityRelations

 * DLN cl_DeliveryNotes
 * DLR cl_DeliveryRelations
 * DPN cl_DispatchNotes
 * DPR cl_DispatchRelations
 * GND cl_Genders
 
 * GNR cl_GenderRelations
 * INN cl_InventNotes
 * INR cl_InventRelations
 * IVT cl_Inventories
 * IVR cl_InventoryRelations
 
 * LGR cl_LanguageRelations
 * LNG cl_Languages
 * MFR cl_Manufacturers
 * OCP cl_Occupations
 * OCR cl_OccupationRelations
 
 * PKG cl_Packagings
 * PRA cl_ProductAttributes
 * PRD cl_ProductCategories
 * PRL cl_Profiles
 * PRM cl_Permissions
 
 * PRR cl_Role_*
 * PRT cl_Products
 * ROL cl_Roles
 * ROR cl_RoleRelations
 * RTN cl_ReturnNotes
 
 * RTR cl_ReturnRelations
 * SKR cl_StockRelations
 * STK cl_Stocks
 * STR cl_StatusRelations
 * STS cl_Status
 
 * SUP cl_Suppliers
 * TRK cl_Trackings
 * TRN cl_TransferNotes
 * TRR cl_TransferRelations
 * TTL cl_Titles
 
 * TTR cl_TitleRelations
 * UNT cl_Units
 * WRH cl_Warehouses
 * WSN cl_WasteNotes
 * WSR cl_WasteRelations
 */