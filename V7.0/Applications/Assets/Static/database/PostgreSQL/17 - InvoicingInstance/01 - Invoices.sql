/* Invoicing app */

/* Customers, DeliveryNotes, DeliveryRelations, DispatchNotes, DispatchRelations, Discounts, Taxes, InvoiceStatuses, Invoices, InvoiceRelations, PaymentMethods, Payments */

-- Table: public.cl_Customers

CREATE TABLE IF NOT EXISTS public."cl_Customers"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"CustomerReference" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"CompanyNumber" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"SocialNumber" character varying(50) COLLATE pg_catalog."default",
	"TaxpayerNumber" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"StartDate" timestamp without time zone DEFAULT NOW(),
    "EndDate" timestamp without time zone,
    "IsActive" timestamp without time zone,
    "Description" character varying(50) COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertCustomer(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertCustomer"(
	IN _profileid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Customers'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, NOW(), NULL, NULL, %L);', _tablename, _id, _profileid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CUS');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateCustomer(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateCustomer"(
	IN _id character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Customers';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Description" = %L WHERE "ID" = %L AND "EndDate" IS NULL;', _tablename, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteCustomer(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteCustomer"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Customers';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L AND "EndDate" IS NULL;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DisableCustomer(character varying)

CREATE OR REPLACE PROCEDURE public."p_DisableCustomer"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Customers';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "EndDate" = NOW() WHERE "ID" = %L AND "EndDate" IS NULL;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Customer

CREATE OR REPLACE TRIGGER "Delete_Customer"
    BEFORE DELETE
    ON public."cl_Customers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Customer

CREATE OR REPLACE TRIGGER "Insert_Customer"
    BEFORE INSERT 
    ON public."cl_Customers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Customer

CREATE OR REPLACE TRIGGER "Update_Customer"
    BEFORE UPDATE 
    ON public."cl_Customers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_DeliveryNotes

CREATE TABLE IF NOT EXISTS public."cl_DeliveryNotes"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"DeliveryNumber" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"Reference" character varying(50) COLLATE pg_catalog."default",
	"DeliveryDate" timestamp without time zone DEFAULT NOW(),
	"EditDate" timestamp without time zone DEFAULT NOW(),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertDeliveryNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertDeliveryNote"(
	IN _deliverynumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _deliverydate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DeliveryNotes'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NOW(), NULL, %L);', _tablename, _id, _deliverynumber, _reference, _deliverydate, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'DLN');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateDeliveryNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateDeliveryNote"(
	IN _id character varying(50),
	IN _deliverynumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _deliverydate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DeliveryNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "DeliveryNumber" = %L, "Reference" = %L, "DeliveryDate" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _deliverynumber, _reference, _deliverydate,
	_description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteDeliveryNote(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteDeliveryNote"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DeliveryNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_DeliveryNote

CREATE OR REPLACE TRIGGER "Delete_DeliveryNote"
    BEFORE DELETE
    ON public."cl_DeliveryNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_DeliveryNote

CREATE OR REPLACE TRIGGER "Insert_DeliveryNote"
    BEFORE INSERT
    ON public."cl_DeliveryNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_DeliveryNote

CREATE OR REPLACE TRIGGER "Update_DeliveryNote"
    BEFORE UPDATE 
    ON public."cl_DeliveryNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_DeliveryRelations

CREATE TABLE IF NOT EXISTS public."cl_DeliveryRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"DeliveryID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_DeliveryNotes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_DeliveryRelation" UNIQUE ("DeliveryID", "StockID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertDeliveryRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertDeliveryRelation"(
	IN _stockid character varying(50),
	IN _deliveryid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DeliveryRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L) ON CONFLICT ("DeliveryID", "StockID") DO NOTHING;', _tablename, _id, _stockid, _deliveryid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'SKR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteDeliveryRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteDeliveryRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DeliveryRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_DeliveryRelation

CREATE OR REPLACE TRIGGER "Update_DeliveryRelation"
	BEFORE UPDATE
	ON public."cl_DeliveryRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_DeliveryRelation

CREATE OR REPLACE TRIGGER "Insert_DeliveryRelation"
    BEFORE INSERT
    ON public."cl_DeliveryRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_DeliveryRelation

CREATE OR REPLACE TRIGGER "Remove_DeliveryRelation"
    BEFORE DELETE
    ON public."cl_DeliveryRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_DispatchNotes

CREATE TABLE IF NOT EXISTS public."cl_DispatchNotes"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"DispatchNumber" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"Reference" character varying(50) COLLATE pg_catalog."default",
	"DispatchDate" timestamp without time zone DEFAULT NOW(),
	"EditDate" timestamp without time zone DEFAULT NOW(),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertDispatchNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertDispatchNote"(
	IN _dispatchnumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _dispatchdate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DispatchNotes'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NOW(), NULL, %L);', _tablename, _id, _dispatchnumber, _reference, _dispatchdate, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'DPN');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateDispatchNote(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateDispatchNote"(
	IN _id character varying(50),
	IN _dispatchnumber character varying(50),
	IN _reference character varying(50) DEFAULT NULL::character varying,
	IN _dispatchdate timestamp without time zone DEFAULT NOW(),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DispatchNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "DispatchNumber" = %L, "Reference" = %L, "DispatchDate" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _dispatchnumber, _reference, _dispatchdate,
	_description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteDispatchNote(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteDispatchNote"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DispatchNotes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_DispatchNote

CREATE OR REPLACE TRIGGER "Delete_DispatchNote"
    BEFORE DELETE
    ON public."cl_DispatchNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_DispatchNote

CREATE OR REPLACE TRIGGER "Insert_DispatchNote"
    BEFORE INSERT
    ON public."cl_DispatchNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_DispatchNote

CREATE OR REPLACE TRIGGER "Update_DispatchNote"
    BEFORE UPDATE 
    ON public."cl_DispatchNotes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_DispatchRelations

CREATE TABLE IF NOT EXISTS public."cl_DispatchRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"DispatchID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_DispatchNotes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_DispatchRelation" UNIQUE ("DispatchID", "StockID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertDispatchRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertDispatchRelation"(
	IN _stockid character varying(50),
	IN _dispatchid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DispatchRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L) ON CONFLICT ("DispatchID", "StockID") DO NOTHING;', _tablename, _id, _stockid, _dispatchid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'SKR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteDispatchRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteDispatchRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_DispatchRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_DispatchRelation

CREATE OR REPLACE TRIGGER "Update_DispatchRelation"
	BEFORE UPDATE
	ON public."cl_DispatchRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_DispatchRelation

CREATE OR REPLACE TRIGGER "Insert_DispatchRelation"
    BEFORE INSERT
    ON public."cl_DispatchRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_DispatchRelation

CREATE OR REPLACE TRIGGER "Remove_DispatchRelation"
    BEFORE DELETE
    ON public."cl_DispatchRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_Discounts

CREATE TABLE IF NOT EXISTS public."cl_Discounts"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"DiscountType" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK ("DiscountType" IN ('RATE', 'AMOUNT'));
	"Value" numeric(8,2) NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertDiscount(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertDiscount"(
	IN _name character varying(50),
	IN _discounttype character varying(50),
	IN _value numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Discounts'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _discounttype, _value, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'DCT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateDiscount(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateDiscount"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _discounttype character varying(50),
	IN _value numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Discounts';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "DiscountType" = %L, "Value" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _discounttype, _value, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteDiscount(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteDiscount"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Discounts';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Discount

CREATE OR REPLACE TRIGGER "Delete_Discount"
    BEFORE DELETE
    ON public."cl_Discounts"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Discount

CREATE OR REPLACE TRIGGER "Insert_Discount"
    BEFORE INSERT 
    ON public."cl_Discounts"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Discount

CREATE OR REPLACE TRIGGER "Update_Discount"
    BEFORE UPDATE 
    ON public."cl_Discounts"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Taxes

CREATE TABLE IF NOT EXISTS public."cl_Taxes"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"TaxType" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK ('RATE', 'AMOUNT'),
	"Value" numeric(8,2) NOT NULL CHECK ("Value" >= 0),
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTaxe(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTaxe"(
	IN _name character varying(50),
	IN _taxtype character varying(50),
	IN _value numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Taxes'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _taxtype, _value, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TAX');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateTaxe(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateTaxe"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _taxtype character varying(50),
	IN _value numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Taxes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "TaxType" = %L, "Value" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _taxtype, _value, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteTaxe(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteTaxe"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Taxes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Taxe

CREATE OR REPLACE TRIGGER "Delete_Taxe"
    BEFORE DELETE
    ON public."cl_Taxes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Taxe

CREATE OR REPLACE TRIGGER "Insert_Taxe"
    BEFORE INSERT 
    ON public."cl_Taxes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Taxe

CREATE OR REPLACE TRIGGER "Update_Taxe"
    BEFORE UPDATE 
    ON public."cl_Taxes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.InvoiceStatuses

DROP TABLE IF EXISTS public."cl_InvoiceStatuses";
CREATE TABLE public."cl_InvoiceStatuses"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertInvoiceStatus(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertInvoiceStatus"(
	IN _name character varying(50),
	IN _code character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InvoiceStatuses'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'INS');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateInvoiceStatus(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateInvoiceStatus"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InvoiceStatuses';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteInvoiceStatus(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteInvoiceStatus"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InvoiceStatuses';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_InvoiceStatus

CREATE OR REPLACE TRIGGER "Delete_InvoiceStatus"
    BEFORE DELETE
    ON public."cl_InvoiceStatuss"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_InvoiceStatus

CREATE OR REPLACE TRIGGER "Insert_InvoiceStatus"
    BEFORE INSERT 
    ON public."cl_InvoiceStatuss"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_InvoiceStatus

CREATE OR REPLACE TRIGGER "Update_InvoiceStatus"
    BEFORE UPDATE 
    ON public."cl_InvoiceStatuss"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Invoices

DROP TABLE IF EXISTS public."cl_Invoices";
CREATE TABLE public."cl_Invoices"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"InvoiceDate" timestamp without time zone NOT NULL,
	"EditDate" timestamp without time zone DEFAULT NOW(),
	"InvoiceNumber" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"CustomerID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Customers" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"InvoiceStatusID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_InvoiceStatuses" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"DispatchNoteID" character varying(50) COLLATE pg_catalog."default" REFERENCES public."cl_DispatchNotes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE SET NULL,
	"DueDate" timestamp without time zone NOT NULL,
	"SubTotal" numeric(8,2) NOT NULL CHECK ("Subtotal" >= 0),
	"TotalDiscount" numeric(8,2) NOT NULL CHECK ("TotalDiscount" >= 0),
	"TotalTax" numeric(8,2) NOT NULL CHECK ("Subtotal" >= 0),
	"TotalAmount" numeric(8,2) NOT NULL CHECK ("Subtotal" >= 0),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertInvoice(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertInvoice"(
	IN _invoicenumber character varying(50),
	IN _customerid character varying(50),
	IN _invoicestatusid character varying(50),
	IN _dispatchnoteid character varying(50) DEFAULT NULL::character varying,
	IN _invoicedate timestamp without time zone DEFAULT NOW(),
	IN _duedate timestamp without time zone DEFAULT NOW(),
	IN _subtotal numeric(8,2),
	IN _totaldiscount numeric(8,2),
	IN _totaltax numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Invoices'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, NOW(), %L, %L, %L, %L, %L, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _invoicedate, _invoicenumber, _customerid, _invoicestatusid, _dispatchnoteid,
	_duedate, _subtotal, _totaldiscount, _totaltax, (_subtotal - _totaldiscount + _totaltax), _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'INV');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateInvoice(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateInvoice"(
	IN _id character varying(50),
	IN _invoicedate timestamp without time zone DEFAULT NOW(),
	IN _duedate timestamp without time zone DEFAULT NOW(),
	IN _subtotal numeric(8,2),
	IN _totaldiscount numeric(8,2),
	IN _totaltax numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Invoices';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "InvoiceDate" = %L, "DueDate" = %L, "SubTotal" = %L, "TotalDiscount" = %L, "TotalTax" = %L, "TotalAmount" = %L, "Description" = %L WHERE "ID" = %L;', _tablename,
	_invoicedate, _duedate, _subtotal, _totaldiscount, _totaltax, (_subtotal - _totaldiscount + _totaltax), _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteInvoice(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteInvoice"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Invoices';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Invoice

CREATE OR REPLACE TRIGGER "Delete_Invoice"
    BEFORE DELETE
    ON public."cl_Invoices"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Invoice

CREATE OR REPLACE TRIGGER "Insert_Invoice"
    BEFORE INSERT
    ON public."cl_Invoices"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Invoice

CREATE OR REPLACE TRIGGER "Update_Invoice"
    BEFORE UPDATE 
    ON public."cl_Invoices"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_InvoiceRelations

DROP TABLE IF EXISTS public."cl_InvoiceRelations";
CREATE TABLE public."cl_InvoiceRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"InvoiceID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Invoices" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Quantity" numeric(8,2) NOT NULL CHECK ("Quantity" >= 0),
	"UnitPrice" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_PriceRelations" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"DiscountID" character varying(50) COLLATE pg_catalog."default" REFERENCES public."cl_discounts" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"TaxID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Taxes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Total" numeric(8,2) NOT NULL CHECK ("Subtotal" >= 0),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_InvoiceRelation" UNIQUE ("InvoiceID", "StockID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertInvoiceRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertInvoiceRelation"(
	IN _stockid character varying(50),
	IN _invoiceid character varying(50),
	IN _quantity numeric(8,2),
	IN _unitprice character varying(50),
	IN _discountid character varying(50),
	IN _taxid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InvoiceRelations'; _id character varying(50) := '%s'; _discount numeric(8,2); _taxvalue numeric(8,2); _price numeric(8,2);
BEGIN
	--
	SELECT "Value" INTO _discount FROM public."cl_Discounts" WHERE "ID" = _discountid;
	SELECT "Value" INTO _taxvalue FROM public."cl_Taxes" WHERE "ID" = _taxid;
	SELECT "UnitPrice" INTO _price FROM public."cl_PriceRelations" WHERE "ID" = _unitprice;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, %L, %L, %L, NULL, %L) ON CONFLICT ("InvoiceID", "StockID") DO NOTHING;', _tablename, _id, _invoiceid, _stockid, _quantity, _unitprice,
	_discountid, _taxid, ((_quantity * _price) - _discount + _taxvalue), _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'INR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteInvoiceRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteInvoiceRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InvoiceRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_InvoiceRelation

CREATE OR REPLACE TRIGGER "Update_InvoiceRelation"
	BEFORE UPDATE
	ON public."cl_InvoiceRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_InvoiceRelation

CREATE OR REPLACE TRIGGER "Insert_InvoiceRelation"
    BEFORE INSERT
    ON public."cl_InvoiceRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_InvoiceRelation

CREATE OR REPLACE TRIGGER "Remove_InvoiceRelation"
    BEFORE DELETE
    ON public."cl_InvoiceRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_PaymentMethods

CREATE TABLE IF NOT EXISTS public."cl_PaymentMethods"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertPaymentMethod(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertPaymentMethod"(
	IN _name character varying(50),
	IN _code character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_PaymentMethods'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PYM');
END;
$BODY$;

-- PROCEDURE: public.p_UpdatePaymentMethod(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdatePaymentMethod"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_PaymentMethods';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeletePaymentMethod(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeletePaymentMethod"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_PaymentMethods';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_PaymentMethod

CREATE OR REPLACE TRIGGER "Delete_PaymentMethod"
    BEFORE DELETE
    ON public."cl_PaymentMethods"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_PaymentMethod

CREATE OR REPLACE TRIGGER "Insert_PaymentMethod"
    BEFORE INSERT 
    ON public."cl_PaymentMethods"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_PaymentMethod

CREATE OR REPLACE TRIGGER "Update_PaymentMethod"
    BEFORE UPDATE 
    ON public."cl_PaymentMethods"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Payments

CREATE TABLE IF NOT EXISTS public."cl_Payments"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"PaymentDate" timestamp without time zone NOT NULL,
	"EditDate" timestamp without time zone NOT NULL,
	"PaymentID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_PaymentMethods" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ReferenceID" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK (public."f_CheckReference"("ReferenceID", "AppID")),-- Invoices, Purchases
	"AppID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Apps" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ReferenceNumber" text COLLATE pg_catalog."default",
	"Amount" numeric(8,2) NOT NULL CHECK ("Amount" >= 0),
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertPayment(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertPayment"(
	IN _paymentdate timestamp without time zone DEFAULT NOW(),
	IN _paymentid character varying(50),
	IN _noteid character varying(50),
	IN _appid character varying(50),
	IN _referencenumber text,
	IN _amount numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Payments'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, NOW(), %L, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _paymentdate, _paymentid, _noteid, _appid, _referencenumber, _amount, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PAY');
END;
$BODY$;

-- FUNCTION: public.t_CheckPayment()

CREATE OR REPLACE FUNCTION public."t_CheckPayment"()
    RETURNS trigger
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE NOT LEAKPROOF
AS $BODY$
DECLARE
    _amount NUMERIC(8,2);
    _total NUMERIC(8,2);
BEGIN
    SELECT "Amount" INTO _amount FROM public."cl_Invoices" WHERE "ID" = NEW."InvoiceID";

	SELECT COALESCE(SUM(Amount), 0) INTO _total FROM "cl_Payments" WHERE "ReferenceID" = NEW."InvoiceID";

	IF _total > _amount THEN
		RAISE EXCEPTION 'Payment amount exceeds invoice limit. Invoice: %, Paid: %, New Payment: %',_amount, _total - NEW.Amount, NEW.Amount;
	END IF;

	RETURN NEW;
END;
$BODY$;

-- Trigger: Check_Payment

CREATE OR REPLACE TRIGGER "Check_Payment"
	BEFORE INSERT OR UPDATE
	ON public."cl_Payments"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_CheckPayment"();

-- Trigger: Delete_Payment

CREATE OR REPLACE TRIGGER "Delete_Payment"
    BEFORE DELETE OR UPDATE
    ON public."cl_Payments"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Payment

CREATE OR REPLACE TRIGGER "Insert_Payment"
    BEFORE INSERT 
    ON public."cl_Payments"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();