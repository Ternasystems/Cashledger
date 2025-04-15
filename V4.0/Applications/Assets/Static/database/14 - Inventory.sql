/* Inventory app */

-- Table: public.cl_Customers

CREATE TABLE IF NOT EXISTS public."cl_Customers"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
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
    BEFORE INSERT OR UPDATE 
    ON public."cl_Customers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Customer

CREATE OR REPLACE TRIGGER "Update_Customer"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Customers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Suppliers

CREATE TABLE IF NOT EXISTS public."cl_Suppliers"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"StartDate" timestamp without time zone NOT NULL,
    "EndDate" timestamp without time zone,
    "IsActive" timestamp without time zone,
    "Description" character varying(50) COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertSupplier(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertSupplier"(
	IN _profileid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Suppliers'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, NOW(), NULL, NULL, %L);', _tablename, _id, _profileid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'SUP');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateSupplier(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateSupplier"(
	IN _id character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Suppliers';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Description" = %L WHERE "ID" = %L AND "EndDate" IS NULL;', _tablename, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteSupplier(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteSupplier"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Suppliers';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L AND "EndDate" IS NULL;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DisableSupplier(character varying)

CREATE OR REPLACE PROCEDURE public."p_DisableSupplier"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Suppliers';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "EndDate" = NOW() WHERE "ID" = %L AND "EndDate" IS NULL;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Supplier

CREATE OR REPLACE TRIGGER "Delete_Supplier"
    BEFORE DELETE
    ON public."cl_Suppliers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Supplier

CREATE OR REPLACE TRIGGER "Insert_Supplier"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Suppliers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Supplier

CREATE OR REPLACE TRIGGER "Update_Supplier"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Suppliers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_ProductCategories

CREATE TABLE IF NOT EXISTS public."cl_ProductCategories"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
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
    BEFORE INSERT OR UPDATE 
    ON public."cl_ProductCategories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_ProductCategory

CREATE OR REPLACE TRIGGER "Update_ProductCategory"
    BEFORE INSERT OR UPDATE 
    ON public."cl_ProductCategories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Warehouses

CREATE TABLE IF NOT EXISTS public."cl_Warehouses"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"Location" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertWarehouse(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertWarehouse"(
	IN _name character varying(50),
	IN _location character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Warehouses'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _location, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'WRH');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateWarehouse(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateWarehouse"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _location character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Warehouses';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Location" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _location, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteWarehouse(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteWarehouse"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Warehouses';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Warehouse

CREATE OR REPLACE TRIGGER "Delete_Warehouse"
    BEFORE DELETE
    ON public."cl_Warehouses"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Warehouse

CREATE OR REPLACE TRIGGER "Insert_Warehouse"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Warehouses"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Warehouse

CREATE OR REPLACE TRIGGER "Update_Warehouse"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Warehouses"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Manufacturers

CREATE TABLE IF NOT EXISTS public."cl_Manufacturers"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertManufacturer(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertManufacturer"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Manufacturers'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'MFR');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateManufacturer(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateManufacturer"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Manufacturers';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteManufacturer(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteManufacturer"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Manufacturers';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Manufacturer

CREATE OR REPLACE TRIGGER "Delete_Manufacturer"
    BEFORE DELETE
    ON public."cl_Manufacturers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Manufacturer

CREATE OR REPLACE TRIGGER "Insert_Manufacturer"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Manufacturers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Manufacturer

CREATE OR REPLACE TRIGGER "Update_Manufacturer"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Manufacturers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Units

CREATE TABLE IF NOT EXISTS public."cl_Units"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
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
    BEFORE INSERT OR UPDATE 
    ON public."cl_Units"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Unit

CREATE OR REPLACE TRIGGER "Update_Unit"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Units"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- table: public.cl_Packagings

CREATE TABLE IF NOT EXISTS public."cl_Packagings"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
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
    BEFORE INSERT OR UPDATE 
    ON public."cl_Packagings"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Packaging

CREATE OR REPLACE TRIGGER "Update_Packaging"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Packagings"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Products

CREATE TABLE IF NOT EXISTS public."cl_Products"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"CategoryID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_ProductCategories" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"UnitID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Units" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"MinStock" numeric(8,2) NOT NULL CHECK ("MinStock" >= 0),
	"MaxStock" numeric(8,2) NOT NULL CHECK ("MaxStock" >= 0),
	"StartDate" timestamp without time zone DEFAULT NOW(),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertProduct(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertProduct"(
	IN _name character varying(50),
	IN _categoryid character varying(50),
	IN _unitid character varying(50),
	IN _minstock numeric(8,2),
	IN _maxstock numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Products'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, %L, %L, NOW(), NULL, %L);', _tablename, _id, _code, _categoryid, _name, _unitid, _minstock, _maxstock, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PRT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateProduct(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateProduct"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _unitid character varying(50),
	IN _minstock numeric(8,2),
	IN _maxstock numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Products';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "UnitID" = %L, "MinStock" = %L, "MaxStock" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _unitid, _minstock, _maxstock, _description, _id);
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
    BEFORE INSERT OR UPDATE 
    ON public."cl_Products"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Product

CREATE OR REPLACE TRIGGER "Update_Product"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Products"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_ProductAttributes

CREATE TABLE IF NOT EXISTS public."cl_ProductAttributes"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"AttributeType" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK ("AttributeType" IN ('text', 'number', 'boolean', 'timestamp')),
	"AttributeConstraint" text COLLATE pg_catalog."default",
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertProductAttribute(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertProductAttribute"(
	IN _name character varying(50),
	IN _attributetype character varying(50),
	IN _attributeconstraint text DEFAULT NULL::text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ProductAttributes'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _attributetype, _attributeconstraint, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PRA');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateProductAttribute(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateProductAttribute"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _type character varying(50),
	IN _constraint text DEFAULT NULL::text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ProductAttributes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "AttributeType" = %L, "AttributeConstraint" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _type, _constraint, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteProductAttribute(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteProductAttribute"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ProductAttributes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_ProductAttribute

CREATE OR REPLACE TRIGGER "Delete_ProductAttribute"
    BEFORE DELETE
    ON public."cl_ProductAttributes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_ProductAttribute

CREATE OR REPLACE TRIGGER "Insert_ProductAttribute"
    BEFORE INSERT OR UPDATE 
    ON public."cl_ProductAttributes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_ProductAttribute

CREATE OR REPLACE TRIGGER "Update_ProductAttribute"
    BEFORE INSERT OR UPDATE 
    ON public."cl_ProductAttributes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_AttributeRelations

CREATE TABLE IF NOT EXISTS public."cl_AttributeRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"AttributeID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_ProductAttributes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ProductID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Products" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Value" text COLLATE pg_catalog."default" NOT NULL,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_AttributeRelation" UNIQUE ("AttributeID", "ProductID", "Value")
)

TABLESPACE pg_default;

-- FUNCTION: public.t_CheckAttribute()

CREATE OR REPLACE FUNCTION public."t_CheckAttribute"()
	RETURNS trigger
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE NOT LEAKPROOF
AS $BODY$
DECLARE _type character varying(50); _constraint text; _query text; _valid boolean;
BEGIN
	SELECT "AttributeType", "AttributeConstraint" INTO _type, _constraint FROM public."cl_ProductAttributes" WHERE "ID" = NEW."AttributeID";
	--
	IF _type = 'number' THEN
		PERFORM NEW."Value"::NUMERIC;
	ELSIF _type = 'boolean' THEN
		PERFORM NEW."Value"::BOOLEAN;
	ELSIF _type = 'timestamp' THEN
		PERFORM NEW."Value"::TIMESTAMP;
	ELSIF _type = 'text' THEN
		PERFORM NEW."Value"::TEXT;
	ELSE
		RAISE EXCEPTION 'Unsupported attribute type: %', _type;
	END IF;
	--
	IF _constraint IS NOT NULL AND _constraint != '' THEN
		_query := FORMAT('SELECT (%s)', REPLACE(_constraint, 'VALUE', NEW."Value"));
		EXECUTE _query INTO _valid;
		--
		IF NOT _valid THEN
			RAISE EXCEPTION 'Attribute constraint failed: Value[%] does not meet constraint [%]', NEW."Value", _constraint;
		END IF;
	END IF;
	--
	RETURN NEW;
END;
$BODY$;

-- PROCEDURE: public.p_InsertAttributeRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertAttributeRelation"(
	IN _attributeid character varying(50),
	IN _productid character varying(50),
	IN _value text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_AttributeRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L);', _tablename, _id, _attributeid, _productid, _value, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'ATR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteAttributeRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteAttributeRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_AttributeRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_AttributeRelation

CREATE OR REPLACE TRIGGER "Update_AttributeRelation"
	BEFORE UPDATE
	ON public."cl_AttributeRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_AttributeRelation

CREATE OR REPLACE TRIGGER "Insert_AttributeRelation"
    BEFORE INSERT OR DELETE
    ON public."cl_AttributeRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Table: public.cl_Stocks

CREATE TABLE IF NOT EXISTS public."cl_Stocks"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"ProductID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Products" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"UnitID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Units" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"WarehouseID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Warehouses" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"PackagingID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Packagings" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"StockDate" timestamp without time zone DEFAULT NOW(),
	"BatchNumber" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"LastChecked" timestamp without time zone DEFAULT NOW(),
	"Quantity" numeric(8,2) NOT NULL CHECK ("Quantity" >= 0),
	"UnitCost" numeric(8,2) NOT NULL CHECK ("UnitCost" >= 0),
	"UnitPrice" numeric(8,2) NOT NULL CHECK ("UnitPrice" >= 0),
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_Stock" UNIQUE ("ProductID", "WarehouseID", "PackagingID", "BatchNumber")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertStock(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertStock"(
	IN _productid character varying(50),
	IN _unitid character varying(50),
	IN _warehouseid character varying(50),
	IN _packagingid character varying(50),
	IN _batchnumber character varying(50),
	IN _quantity numeric(8,2),
	IN _unitcost numeric(8,2),
	IN _unitprice numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Stocks'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NOW(), %L, %L,  %L, %L, %L, %L, NULL, %L);', _tablename, _id, _code, _productid, _unitid, _warehouseid, _packagingid, _batchnumber, _quantity,
	_unitcost, _unitprice, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'STK');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateStock(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateStock"(
	IN _id character varying(50),
	IN _warehouseid character varying(50),
	IN _packagingid character varying(50),
	IN _batchnumber character varying(50),
	IN _unitcost numeric(8,2),
	IN _unitprice numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Stocks';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "WarehouseID" = %L, "PackagingID" = %L, "BatchNumber" = %L, "UnitCost" = %L, "UnitPrice" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _warehouseid,
	_packagingid, _batchnumber, _unitcost, _unitprice, _description, _id);
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
    BEFORE INSERT OR UPDATE 
    ON public."cl_Stocks"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Stock

CREATE OR REPLACE TRIGGER "Update_Stock"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Stocks"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_StockRelations

CREATE TABLE IF NOT EXISTS public."cl_StockRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"AttributeID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_ProductAttributes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Value" text COLLATE pg_catalog."default" NOT NULL,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_StockRelation" UNIQUE ("AttributeID", "StockID", "Value")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertStockRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertStockRelation"(
	IN _attributeid character varying(50),
	IN _stockid character varying(50),
	IN _value text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_StockRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L);', _tablename, _id, _attributeid, _stockid, _value, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'SKR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteStockRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteStockRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_StockRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_StockRelation

CREATE OR REPLACE TRIGGER "Update_StockRelation"
	BEFORE UPDATE
	ON public."cl_StockRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_StockRelation

CREATE OR REPLACE TRIGGER "Insert_StockRelation"
    BEFORE INSERT OR DELETE
    ON public."cl_StockRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- FUNCTION: public."f_CheckInventory"(character varying);

CREATE OR REPLACE FUNCTION public."f_CheckInventory"(_partnerid character varying(50))
	RETURNS boolean
	LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
BEGIN
    IF EXISTS (SELECT 1 FROM public."cl_Suppliers" WHERE "ID" = "PartnerID") OR EXISTS (SELECT 1 FROM public."cl_Customers" WHERE "ID" = "partnerID") THEN
		RETURN TRUE;
	END IF;
	RETURN FALSE;
END;
$BODY$;

-- Table: public.cl_Inventories

CREATE TABLE IF NOT EXISTS public."cl_Inventories"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"StockID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Stocks" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"UnitID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Units" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"PartnerID" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"InventoryType" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK ("InventoryType" IN ('IN', 'OUT', 'RETURN', 'WASTE', 'INVENT', 'TRANSFER')),
	"Quantity" numeric(8,2) NOT NULL CHECK ("Quantity" >= 0),
	"InventDate" timestamp without time zone DEFAULT NOW(),
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "CT_Inventory" CHECK (public."f_CheckInventory"("PartnerID"))
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertInventory(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertInventory"(
	IN _stockid character varying(50),
	IN _unitid character varying(50),
	IN _partnerid character varying(50),
	IN _inventorytype character varying(50),
	IN _quantity numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Inventories'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, %L, NOW(), NULL, %L);', _tablename, _id, _stockid, _unitid, _partnerid, _inventorytype, _quantity, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'IVT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateInventory(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateInventory"(
	IN _id character varying(50),
	IN _quantity numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Inventories';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Quantity" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _quantity, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteInventory(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteInventory"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Inventories';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Inventory

CREATE OR REPLACE TRIGGER "Delete_Inventory"
    BEFORE DELETE
    ON public."cl_Inventories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Inventory

CREATE OR REPLACE TRIGGER "Insert_Inventory"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Inventories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Inventory

CREATE OR REPLACE TRIGGER "Update_Inventory"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Inventories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_InventoryRelations

CREATE TABLE IF NOT EXISTS public."cl_InventoryRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"InventID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Inventories" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"CredentialID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Credentials" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_InventoryRelation" UNIQUE ("InventID", "CredentialID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertInventoryRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertInventoryRelation"(
	IN _inventid character varying(50),
	IN _credentialid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InventoryRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _inventid, _credentialid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'IVR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteInventoryRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteInventoryRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_InventoryRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_InventoryRelation

CREATE OR REPLACE TRIGGER "Update_InventoryRelation"
	BEFORE UPDATE
	ON public."cl_InventoryRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_InventoryRelation

CREATE OR REPLACE TRIGGER "Insert_InventoryRelation"
    BEFORE INSERT OR DELETE
    ON public."cl_InventoryRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Initial data

DO
$BODY$
DECLARE _id character varying(50); _appid character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50); _es character varying(50); _ar character varying(50);

BEGIN
	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	SELECT "ID" INTO _es FROM public."cl_Languages" WHERE "Label" = 'ES';
	SELECT "ID" INTO _ar FROM public."cl_Languages" WHERE "Label" = 'AR';

	-- App registry

	CALL public."p_InsertApp"('Inventory');
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Inventory';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Inventory');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Inventory');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Gestion des stocks');

	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Sales') THEN
		CALL public."p_InsertAppCategory"('Sales');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Sales';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Sales');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Ventes');
	END IF;
	--
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'Purchases') THEN
		CALL public."p_InsertAppCategory"('Purchases');
		SELECT "ID" INTO _id from public."cl_AppCategories" WHERE "Name" = 'Purchases';
		CALL public."p_InsertLanguageRelation"(_us, _id, 'Purchases');
		CALL public."p_InsertLanguageRelation"(_gb, _id, 'Purchases');
		CALL public."p_InsertLanguageRelation"(_fr, _id, 'Achats');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Inventory';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Sales';
	CALL public."p_InsertAppRelation"(_id, _appid);
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'Purchases';
	CALL public."p_InsertAppRelation"(_id, _appid);

	-- Profiles

	CALL public."p_InsertProfile"('Anonymous F', LOCALTIMESTAMP);
	CALL public."p_InsertProfile"('Anonymous M', LOCALTIMESTAMP);
	CALL public."p_InsertProfile"('Anonymous', LOCALTIMESTAMP);

	-- Insert TitleRelations

	SELECT "ID" INTO _id FROM public."cl_Titles" WHERE "Code" = 1;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertTitleRelation"(_id, _appid);
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertTitleRelation"(_id, _appid);
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertTitleRelation"(_id, _appid);

	-- Insert StatusRelations

	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Code" = 1;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertStatusRelation"(_id, _appid);
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertStatusRelation"(_id, _appid);
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertStatusRelation"(_id, _appid);

	-- Insert GenderRelations

	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 3;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertGenderRelation"(_id, _appid);
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 2;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertGenderRelation"(_id, _appid);
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 1;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertGenderRelation"(_id, _appid);

	-- Insert CivilityRelations

	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 3;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertCivilityRelation"(_id, _appid);
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 2;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertCivilityRelation"(_id, _appid);
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 1;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertCivilityRelation"(_id, _appid);

	-- Insert OccupationRelations

	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Code" = 1;
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertOccupationRelation"(_id, _appid);
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertOccupationRelation"(_id, _appid);
	SELECT "ID" INTO _appid FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertOccupationRelation"(_id, _appid);

	-- Insert Customers

	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertCustomer"(_id);
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertCustomer"(_id);
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertCustomer"(_id);

	-- Insert Suppliers

	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous';
	CALL public."p_InsertSupplier"(_id);
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous M';
	CALL public."p_InsertSupplier"(_id);
	SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Anonymous F';
	CALL public."p_InsertSupplier"(_id);

	-- Insert Warehouse

	CALL public."p_InsertWarehouse"('Anonymous warehouse', 'Anonymous location');

	-- Insert Manufacturer

	CALL public."p_InsertManufacturer"('Anonymous manufacturer');

	-- Insert Units

	CALL public."p_InsertUnit"('Package', 'pkg');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Package';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Package');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Package');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Paquet');
	--
	CALL public."p_InsertUnit"('Meter', 'm');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Meter';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Meter');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Meter');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mètre');
	--
	CALL public."p_InsertUnit"('Foot', 'ft');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Foot';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Foot');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Foot');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Pied');
	--
	CALL public."p_InsertUnit"('Nautical mile', 'n. mile');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Nautical mile';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Nautical mile');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Nautical mile');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mile nautique');
	--
	CALL public."p_InsertUnit"('Liter', 'l');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Liter';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Liter');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Liter');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Litre');
	--
	CALL public."p_InsertUnit"('Gallon', 'gal');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Gallon';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Gallon');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Gallon');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Gallon');
	--
	CALL public."p_InsertUnit"('Second', 's');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Second';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Second');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Second');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Seconde');
	--
	CALL public."p_InsertUnit"('Kilogram', 'kg');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Kilogram';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Kilogram');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Kilogram');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Kilogramme');
	--
	CALL public."p_InsertUnit"('Pound', 'p');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Pound';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Pound');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Pound');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Livre');
	--
	CALL public."p_InsertUnit"('Radian', 'rd');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Radian';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Radian');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Radian');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Radian');
	--
	CALL public."p_InsertUnit"('Bar', 'bar');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Bar';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Bar');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Bar');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Bar');
	--
	CALL public."p_InsertUnit"('Unit', 'u');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Unit';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Unit');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Unit');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Unité');
	--
	CALL public."p_InsertUnit"('Pixel', 'px');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Pixel';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Pixel');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Pixel');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Pixel');
	--
	CALL public."p_InsertUnit"('Joule', 'j');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Joule';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Joule');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Joule');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Joule');
	--
	CALL public."p_InsertUnit"('Hertz', 'Hz');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Hertz';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Hertz');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Hertz');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Hertz');
	--
	CALL public."p_InsertUnit"('Farenheit', '°F');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Farenheit';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Farenheit');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Farenheit');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Farenheit');
	--
	CALL public."p_InsertUnit"('Celsius', '°C');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Degree Celsius';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Degree Celsius');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Degree Celsius');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Degré Celsius');
	--
	CALL public."p_InsertUnit"('Kelvin', '°K');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Kelvin';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Kelvin');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Kelvin');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Kelvin');
	--
	CALL public."p_InsertUnit"('Byte', 'b');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Byte';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Byte');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Byte');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Octet');
	--
	CALL public."p_InsertUnit"('USD', '$');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'USD';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'USD');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'USD');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Dollar US');
	--
	CALL public."p_InsertUnit"('EURO', '€');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Euro';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Euro');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Euro');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Euro');
	--
	CALL public."p_InsertUnit"('XAF', 'CFAF');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'CFAF';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'CFAF');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'CFAF');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'FCFA');
	--
	CALL public."p_InsertUnit"('XOF', 'CFAF');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'CFAF';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'CFAF');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'CFAF');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'FCFA');
	--
	CALL public."p_InsertUnit"('Milli', 'm');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Milli';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Milli');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Milli');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Milli');
	--
	CALL public."p_InsertUnit"('Centi', 'c');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Centi';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Centi');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Centi');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Centi');
	--
	CALL public."p_InsertUnit"('Deci', 'd');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Deci';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Deci');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Deci');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Déci');
	--
	CALL public."p_InsertUnit"('Kilo', 'K');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Kilo';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Kilo');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Kilo');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Kilo');
	--
	CALL public."p_InsertUnit"('Quinta', 'q');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Quinta';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Quinta');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Quinta');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Quinta');
	--
	CALL public."p_InsertUnit"('Giga', 'G');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Giga';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Giga');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Giga');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Giga');
	--
	CALL public."p_InsertUnit"('Mega', 'M');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Mega';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Mega');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Mega');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mega');
	--
	CALL public."p_InsertUnit"('Tera', 'T');
	SELECT "ID" INTO _id FROM public."cl_Units" WHERE "Name" = 'Tera';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Tera');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Tera');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Tera');

	-- Insert Product categories

	CALL public."p_InsertProductCategory"('General pharmaceuticals');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'General pharmaceuticals';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'General pharmaceuticals');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'General pharmaceuticals');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Médicaments généraux');
	--
	CALL public."p_InsertProductCategory"('Fertility Pharmaceuticals');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'Fertility Pharmaceuticals';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Fertility Pharmaceuticals');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Fertility Pharmaceuticals');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Médicaments fertilité');
	--
	CALL public."p_InsertProductCategory"('Medical consumables');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'Medical consumables';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Medical consumables');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Medical consumables');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Consommables médicaux');
	--
	CALL public."p_InsertProductCategory"('Fertility consumables');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'Fertility consumables';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Fertility consumables');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Fertility consumables');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Consommables fertilité');
	--
	CALL public."p_InsertProductCategory"('Laboratory reagents');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'Laboratory reagents';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Laboratory reagents');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Laboratory reagents');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Réactifs de laboratoire');
	--
	CALL public."p_InsertProductCategory"('Medical gases');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'Medical gases';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Medical gases');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Medical gases');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Gaz médicaux');
	--
	CALL public."p_InsertProductCategory"('Culture media');
	SELECT "ID" INTO _id FROM public."cl_ProductCategories" WHERE "Name" = 'Culture media';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Culture media');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Culture media');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Milieux de culture');

	-- ProductAttributes

	CALL public."p_InsertProductAttribute"('Commercial denomination', 'text');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'Commercial denomination';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Commercial denomination');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Commercial denomination');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Dénomination commerciale');
	--
	CALL public."p_InsertProductAttribute"('International denomination', 'text');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'International denomination';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'International denomination (INN)');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'International denomination (INN)');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Dénomination internationale (DCI)');
	--
	CALL public."p_InsertProductAttribute"('Manufacturer', 'text', 'VALUE IN (SELECT "ID" FROM public."cl_Manufacturers")');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'Manufacturer';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Manufacturer');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Manufacturer');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Fabricant');
	--
	CALL public."p_InsertProductAttribute"('Dosage', 'number');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'Dosage';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Dosage');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Dosage');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Dosage');
	--
	CALL public."p_InsertProductAttribute"('Drug route', 'text');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'Drug route';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Drug route');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Drug route');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Voie d''Administration');
	--
	CALL public."p_InsertProductAttribute"('Galenics', 'text');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'Galenics';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Galenics');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Galenics');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Galénique');
	--
	/*CALL public."p_InsertProductAttribute"('Expiration date', 'timestamp');
	SELECT "ID" INTO _id FROM public."cl_ProductAttributes" WHERE "Name" = 'Expiration date';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Expiration date');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Expiration date');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Date de péremption');*/

	-- Insert Packagings

	CALL public."p_InsertPackaging"('Package of 12');
	SELECT "ID" INTO _id FROM public."cl_Packagings" WHERE "Name" = 'Package of 12';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Package of 12');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Package of 12');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Paquet de 12');
	--
	CALL public."p_InsertPackaging"('Box of 8 ampoules');
	SELECT "ID" INTO _id FROM public."cl_Packagings" WHERE "Name" = 'Box of 8 ampoules';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Box of 8 ampoules');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Box of 8 ampoules');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Boîte de 8 ampoules');
	--
	CALL public."p_InsertPackaging"('Bottle of 10ml');
	SELECT "ID" INTO _id FROM public."cl_Packagings" WHERE "Name" = 'Bottle of 10ml';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Bottle of 10ml');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Bottle of 10ml');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Flacon de 10ml');

	-- Insert Products

	CALL public."p_InsertProduct"('Aspirin', '8154220001', '4634220001', 1, 5);
	SELECT "ID" INTO _id FROM public."cl_Products" WHERE "Name" = 'Aspirin';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Aspirin');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Aspirin');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Aspirine');
	
END $BODY$;

-- Trigger: Check_ProductAttribute

CREATE OR REPLACE TRIGGER "Check_ProductAttribute"
	BEFORE INSERT OR UPDATE
	ON public."cl_AttributeRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_CheckAttribute"();

-- Trigger: Check_StockAttribute

CREATE OR REPLACE TRIGGER "Check_StockAttribute"
	BEFORE INSERT OR UPDATE
	ON public."cl_StockRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_CheckAttribute"();

-- Trigger: Log_Audit
DO
$BODY$
DECLARE _tablename text; _triggername text;
	_tablenames text[] := ARRAY['cl_Customers', 'cl_Suppliers', 'cl_ProductCategories', 'cl_Warehouses', 'cl_Manufacturers', 'cl_Units', 'cl_Packagings', 'cl_Products',
	'cl_ProductAttributes', 'cl_AttributeRelations', 'cl_Stocks', 'cl_StockRelations', 'cl_Inventories', 'cl_InventoryRelations'];
BEGIN
	FOR _tablename IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
	LOOP
		IF _tablename != ALL(_tablenames) THEN CONTINUE; END IF;
		_triggername :=
			CASE
				WHEN _tablename = 'cl_ProductCategories' THEN 'Category'
				WHEN _tablename = 'cl_Inventories' THEN 'Inventory'
				ELSE REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 1), 's$', '')
			END;
		_triggername := 'Log_' || _triggername;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
				AFTER INSERT OR UPDATE OR DELETE
				ON public.%I
				FOR EACH ROW
				EXECUTE FUNCTION public."t_LogAudit"();
		', _triggername, _tablename);
		--
		_triggername :=
			CASE
				WHEN _tablename = 'cl_ProductCategories' THEN 'Category'
				WHEN _tablename = 'cl_Inventories' THEN 'Inventory'
				ELSE REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 1), 's$', '')
			END;
			_triggername := 'Release_' || _triggername;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
				AFTER INSERT OR UPDATE OR DELETE
				ON public.%I
				FOR EACH ROW
				EXECUTE FUNCTION public."t_ReleaseThread"();
		', _triggername, _tablename);
	END LOOP;
END $BODY$;