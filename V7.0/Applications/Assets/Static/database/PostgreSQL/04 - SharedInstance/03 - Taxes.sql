/* Shared entities */

/* Taxes */

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