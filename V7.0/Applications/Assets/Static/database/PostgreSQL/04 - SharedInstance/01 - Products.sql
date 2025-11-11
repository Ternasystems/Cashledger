/* Shared entities */

/* ProductCategories, Units, Packagings, Products, Stocks */

-- Table: public.cl_ProductCategories

CREATE TABLE IF NOT EXISTS public."cl_ProductCategories"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertProductCategory(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertProductCategory"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ProductCategories'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PRD');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateProductCategory(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateProductCategory"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ProductCategories';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteProductCategory(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteProductCategory"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ProductCategories';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_ProductCategory

CREATE OR REPLACE TRIGGER "Delete_ProductCategory"
    BEFORE DELETE
    ON public."cl_ProductCategories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_ProductCategory

CREATE OR REPLACE TRIGGER "Insert_ProductCategory"
    BEFORE INSERT 
    ON public."cl_ProductCategories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_ProductCategory

CREATE OR REPLACE TRIGGER "Update_ProductCategory"
    BEFORE UPDATE 
    ON public."cl_ProductCategories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Units

CREATE TABLE IF NOT EXISTS public."cl_Units"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"Label" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertUnit(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertUnit"(
	IN _name character varying(50),
	IN _label character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Units'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _label, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'UNT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateUnit(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateUnit"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _label character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Units';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Label" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _label, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteUnit(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteUnit"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Units';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Unit

CREATE OR REPLACE TRIGGER "Delete_Unit"
    BEFORE DELETE
    ON public."cl_Units"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Unit

CREATE OR REPLACE TRIGGER "Insert_Unit"
    BEFORE INSERT 
    ON public."cl_Units"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Unit

CREATE OR REPLACE TRIGGER "Update_Unit"
    BEFORE UPDATE 
    ON public."cl_Units"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- table: public.cl_Packagings

CREATE TABLE IF NOT EXISTS public."cl_Packagings"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertPackaging(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertPackaging"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Packagings'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PKG');
END;
$BODY$;

-- PROCEDURE: public.p_UpdatePackaging(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdatePackaging"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Packagings';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeletePackaging(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeletePackaging"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Packagings';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Packaging

CREATE OR REPLACE TRIGGER "Delete_Packaging"
    BEFORE DELETE
    ON public."cl_Packagings"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Packaging

CREATE OR REPLACE TRIGGER "Insert_Packaging"
    BEFORE INSERT 
    ON public."cl_Packagings"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Packaging

CREATE OR REPLACE TRIGGER "Update_Packaging"
    BEFORE UPDATE 
    ON public."cl_Packagings"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Products

CREATE TABLE IF NOT EXISTS public."cl_Products"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"CategoryID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_ProductCategories" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"StartDate" timestamp without time zone DEFAULT NOW(),
	"EndDate" timestamp without time zone,
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertProduct(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertProduct"(
	IN _name character varying(50),
	IN _categoryid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Products'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NOW(), NULL, NULL, %L);', _tablename, _id, _code, _categoryid, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PRT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateProduct(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateProduct"(
	IN _id character varying(50),
	IN _categoryid character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Products';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "CategoryID" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _categoryid, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteProduct(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteProduct"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Products';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Product

CREATE OR REPLACE TRIGGER "Delete_Product"
    BEFORE DELETE
    ON public."cl_Products"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Product

CREATE OR REPLACE TRIGGER "Insert_Product"
    BEFORE INSERT 
    ON public."cl_Products"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Product

CREATE OR REPLACE TRIGGER "Update_Product"
    BEFORE UPDATE 
    ON public."cl_Products"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Stocks

CREATE TABLE IF NOT EXISTS public."cl_Stocks"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"StockDate" timestamp without time zone DEFAULT NOW(),
	"ProductID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Products" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"UnitID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Units" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"PackagingID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Packagings" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"BatchNumber" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"ExpiryDate" timestamp without time zone NOT NULL CHECK ("ExpiryDate" > NOW()),
	"LastChecked" timestamp without time zone DEFAULT NOW(),
	"Quantity" numeric(8,2) NOT NULL CHECK ("Quantity" >= 0),
	"UnitCost" numeric(8,2) NOT NULL CHECK ("UnitCost" >= 0),
	"UnitPrice" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_PriceRelations" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"MinStock" numeric(8,2) NOT NULL CHECK ("MinStock" >= 0),
	"MaxStock" numeric(8,2) NOT NULL CHECK ("MaxStock" >= 0),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_Stock" UNIQUE ("ProductID", "PackagingID", "BatchNumber")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertStock(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertStock"(
	IN _productid character varying(50),
	IN _unitid character varying(50),
	IN _packagingid character varying(50),
	IN _batchnumber character varying(50),
	IN _expirydate timestamp without time zone,
	IN _quantity numeric(8,2),
	IN _unitcost numeric(8,2),
	IN _unitprice character varying(50),
	IN _minstock numeric(8,2),
	IN _maxstock numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Stocks'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, NOW(), %L, %L, %L, %L, %L, NOW(), %L, %L, %L, %L, %L, NULL, %L) ON CONFLICT ("ProductID", "PackagingID", "BatchNumber") DO NOTHING;', _tablename, _id,
	_productid, _unitid, _packagingid, _batchnumber, _expirydate, _quantity, _unitcost, _unitprice, _minstock, _maxstock, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'STK');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateStock(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateStock"(
	IN _id character varying(50),
	IN _unitid character varying(50),
	IN _expirydate timestamp without time zone,
	IN _quantity numeric(8,2),
	IN _unitcost numeric(8,2),
	IN _unitprice character varying(50),
	IN _minstock numeric(8,2),
	IN _maxstock numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Stocks';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "UnitID" = %L, "ExpiryDate" = %L, "Quantity" = %L, "UnitCost" = %L, "UnitPrice" = %L, "MinStock" = %L, "MaxStock" = %L, "LastChecked" = NOW(),
	"Description" = %L WHERE "ID" = %L;', _tablename, _unitid, _expirydate, _quantity, _unitcost, _unitprice, _minstock, _maxstock, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_UpdateQuantity(character varying, numeric)

CREATE OR REPLACE PROCEDURE public."p_UpdateQuantity"(
	IN _id character varying(50),
	IN _quantity numeric(8,2))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Stocks';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Quantity" = %L, "LastChecked" = NOW() WHERE "ID" = %L;', _tablename, _quantity, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteStock(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteStock"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Stocks';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Stock

CREATE OR REPLACE TRIGGER "Delete_Stock"
    BEFORE DELETE
    ON public."cl_Stocks"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Stock

CREATE OR REPLACE TRIGGER "Insert_Stock"
    BEFORE INSERT
    ON public."cl_Stocks"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Stock

CREATE OR REPLACE TRIGGER "Update_Stock"
    BEFORE UPDATE 
    ON public."cl_Stocks"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();