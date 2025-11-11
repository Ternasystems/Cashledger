/* Inventory app */

/* ProductCategories, Warehouses, Manufacturers, Units, Packagings, Products, ProductAttributes, AttributeRelations */

-- Table: public.cl_ProductCategories

DROP TABLE IF EXISTS public."cl_ProductCategories";
CREATE TABLE public."cl_ProductCategories"
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

-- Table: public.cl_Warehouses

DROP TABLE IF EXISTS public."cl_Warehouses";
CREATE TABLE public."cl_Warehouses"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
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
    BEFORE INSERT 
    ON public."cl_Warehouses"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Warehouse

CREATE OR REPLACE TRIGGER "Update_Warehouse"
    BEFORE UPDATE 
    ON public."cl_Warehouses"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Manufacturers

DROP TABLE IF EXISTS public."cl_Manufacturers";
CREATE TABLE public."cl_Manufacturers"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
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
    BEFORE INSERT 
    ON public."cl_Manufacturers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Manufacturer

CREATE OR REPLACE TRIGGER "Update_Manufacturer"
    BEFORE UPDATE 
    ON public."cl_Manufacturers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Units

DROP TABLE IF EXISTS public."Units";
CREATE TABLE public."cl_Units"
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

DROP TABLE IF EXISTS public."cl_Packagings";
CREATE TABLE public."cl_Packagings"
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

DROP TABLE IF EXISTS public."cl_Products";
CREATE TABLE public."cl_Products"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"CategoryID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_ProductCategories" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
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

-- Table: public.cl_ProductAttributes

DROP TABLE IF EXISTS public."cl_ProductAttributes";
CREATE TABLE public."cl_ProductAttributes"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"AttributeType" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK ("AttributeType" IN ('text', 'number', 'boolean', 'timestamp', 'table')),
	"AttributeConstraint" text COLLATE pg_catalog."default",
	"AttributeTable" character varying(50) COLLATE pg_catalog."default" REFERENCES public."cl_ReferenceTables" ("TableName") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertProductAttribute(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertProductAttribute"(
	IN _name character varying(50),
	IN _attributetype character varying(50),
	IN _attributeconstraint text DEFAULT NULL::text,
	IN _attributetable character varying(50) DEFAULT NULL::character varying,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ProductAttributes'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _attributetype, _attributeconstraint, _attributetable, _description);
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
	IN _table character varying(50) DEFAULT NULL::character varying,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ProductAttributes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "AttributeType" = %L, "AttributeConstraint" = %L, "AttributeTable" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _type, _constraint,
	_table, _description, _id);
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
    BEFORE INSERT
    ON public."cl_ProductAttributes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_ProductAttribute

CREATE OR REPLACE TRIGGER "Update_ProductAttribute"
    BEFORE UPDATE 
    ON public."cl_ProductAttributes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_AttributeRelations

DROP TABLE IF EXISTS public."AttributeRelations";
CREATE TABLE public."cl_AttributeRelations"
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
	ELSIF _type IN ('text', 'table') THEN
		PERFORM NEW."Value"::TEXT;
	ELSE
		RAISE EXCEPTION 'Unsupported attribute type: %', _type;
	END IF;
	--
	IF _constraint IS NOT NULL AND _constraint != '' THEN
		_query := FORMAT('SELECT (%s)', REPLACE(_constraint, 'VALUE', '$1'));
		EXECUTE _query INTO _valid USING NEW."Value";
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
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L) ON CONFLICT ("AttributeID", "ProductID", "Value") DO NOTHING;', _tablename, _id, _attributeid, _productid, _value, _description);
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
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
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
    BEFORE INSERT
    ON public."cl_AttributeRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_AttributeRelation

CREATE OR REPLACE TRIGGER "Remove_AttributeRelation"
	BEFORE DELETE
	ON public."cl_AttributeRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_RemoveTrigger"();