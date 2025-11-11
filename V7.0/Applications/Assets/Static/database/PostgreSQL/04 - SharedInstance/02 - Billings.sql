/* Shared entities */

/* Currencies, Prices, PriceRelations, PaymentMethods, Discounts */

-- Table: public.cl_Currencies

CREATE TABLE IF NOT EXISTS public."cl_Currencies"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" character(3) UNIQUE NOT NULL,
	"Label" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertCurrency(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertCurrency"(
	IN _code character(3),
	IN _label character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Currencies'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _label, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CUR');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateCurrency(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateCurrency"(
	IN _id character varying(50),
	IN _code character(3),
	IN _label character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Currencies';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Code" = %L, "Label" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _code, _label, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteCurrency(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteCurrency"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Currencies';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Currency

CREATE OR REPLACE TRIGGER "Delete_Currency"
    BEFORE DELETE
    ON public."cl_Currencies"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Currency

CREATE OR REPLACE TRIGGER "Insert_Currency"
    BEFORE INSERT 
    ON public."cl_Currencies"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Currency

CREATE OR REPLACE TRIGGER "Update_Currency"
	BEFORE UPDATE
	ON public."cl_Currencies"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Prices

CREATE TABLE IF NOT EXISTS public."cl_Prices"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"CurrencyID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Currencies" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"StartDate" timestamp without time zone DEFAULT NOW(),
	"EndDate" timestamp without time zone,
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertPrice(character varying, text, text)

CREATE OR REPLACE PROCEDURE public."p_InsertPrice"(
	IN _currencyid character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Prices'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NOW(), NULL, NULL, %L);', _tablename, _id, _code, _name, _currencyid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PRC');
END;
$BODY$;

-- PROCEDURE: public.p_UpdatePrice(character varying, character varying, text, text)

CREATE OR REPLACE PROCEDURE public."p_UpdatePrice"(
	IN _id character varying(50),
	IN _currencyid character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Prices';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "CurrencyID" = %L, "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _currencyid, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeletePrice(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeletePrice"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Prices';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Price

CREATE OR REPLACE TRIGGER "Delete_Price"
    BEFORE DELETE
    ON public."cl_Prices"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Price

CREATE OR REPLACE TRIGGER "Insert_Price"
    BEFORE INSERT 
    ON public."cl_Prices"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Price

CREATE OR REPLACE TRIGGER "Update_Price"
	BEFORE UPDATE
	ON public."cl_Prices"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_PriceRelations

CREATE TABLE IF NOT EXISTS public."cl_PriceRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"PriceID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Prices" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"UnitPrice" numeric(8,2) NOT NULL CHECK ("UnitPrice" >= 0),
	"StartDate" timestamp without time zone DEFAULT NOW(),
	"EndDate" timestamp without time zone,
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_PriceRelation" UNIQUE ("PriceID", "StockID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertPriceRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertPriceRelation"(
	IN _stockid character varying(50),
	IN _priceid character varying(50),
	IN _unitprice numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_PriceRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L. %L, NOW(), NULL, NULL, %L) ON CONFLICT ("PriceID", "StockID") DO NOTHING;', _tablename, _id, _priceid, _stockid, _unitprice, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PCR');
END;
$BODY$;

-- PROCEDURE: public.p_DeletePriceRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeletePriceRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_PriceRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_PriceRelation

CREATE OR REPLACE TRIGGER "Update_PriceRelation"
	BEFORE UPDATE
	ON public."cl_PriceRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_PriceRelation

CREATE OR REPLACE TRIGGER "Insert_PriceRelation"
    BEFORE INSERT
    ON public."cl_PriceRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_PriceRelation

CREATE OR REPLACE TRIGGER "Remove_PriceRelation"
    BEFORE DELETE
    ON public."cl_PriceRelations"
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
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L. %L, %L, NULL, %L);', _tablename, _id, _code, _name, _discounttype, _value, _description);
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