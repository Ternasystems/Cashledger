/* Inventory app */

/* Customers, Suppliers */

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
    BEFORE INSERT 
    ON public."cl_Suppliers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Supplier

CREATE OR REPLACE TRIGGER "Update_Supplier"
    BEFORE UPDATE 
    ON public."cl_Suppliers"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();