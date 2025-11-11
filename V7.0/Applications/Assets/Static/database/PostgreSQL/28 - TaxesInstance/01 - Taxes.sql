/* Taxes app */

/* TaxTypes, Taxes, TaxAttributes, TaxProfiles, TaxRelations, PartnerTaxRelations */

-- Table: public.cl_TaxTypes

DROP TABLE IF EXISTS public."cl_TaxTypes";
CREATE TABLE public."cl_TaxTypes"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTaxType(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTaxType"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TaxTypes'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TXT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateTaxType(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateTaxType"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TaxTypes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteTaxType(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteTaxType"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TaxTypes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_TaxType

CREATE OR REPLACE TRIGGER "Delete_TaxType"
    BEFORE DELETE
    ON public."cl_TaxTypes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TaxType

CREATE OR REPLACE TRIGGER "Insert_TaxType"
    BEFORE INSERT
    ON public."cl_TaxTypes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_TaxType

CREATE OR REPLACE TRIGGER "Update_TaxType"
    BEFORE UPDATE 
    ON public."cl_TaxTypes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Taxes

DROP TABLE IF EXISTS public."cl_Taxes";
CREATE TABLE public."cl_Taxes"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTaxe(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTaxe"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Taxes'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TAX');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateTaxe(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateTaxe"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Taxes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
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

-- Table: public.cl_TaxAttributes

DROP TABLE IF EXISTS public."cl_TaxAttributes";
CREATE TABLE public."cl_TaxAttributes"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "TaxID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Taxes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"TaxTypeID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_TaxeTypes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"TaxValue" numeric(8,2) NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_TaxAttribute" UNIQUE ("TaxID", "TaxTypeID", "TaxValue")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTaxAttribute(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTaxAttribute"(
	IN _taxid character varying(50),
	IN _taxtypeid character varying(50),
	IN _value numeric(8,2),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TaxAttributes'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L) ON CONFLICT ("AttributeID", "ProductID", "Value") DO NOTHING;', _tablename, _id, _taxid, _taxtypeid, _value, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TXA');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateTaxAttribute(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateTaxAttribute"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TaxAttributes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteTaxAttribute(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteTaxAttribute"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TaxAttributes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_TaxAttribute

CREATE OR REPLACE TRIGGER "Delete_TaxAttribute"
    BEFORE DELETE
    ON public."cl_TaxAttributes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TaxAttribute

CREATE OR REPLACE TRIGGER "Insert_TaxAttribute"
    BEFORE INSERT 
    ON public."cl_TaxAttributes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_TaxAttribute

CREATE OR REPLACE TRIGGER "Update_TaxAttribute"
    BEFORE UPDATE 
    ON public."cl_TaxAttributes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_TaxProfiles

DROP TABLE IF EXISTS public."cl_TaxProfiles";
CREATE TABLE public."cl_TaxProfiles"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTaxProfile(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTaxProfile"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TaxProfiles'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TXP');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateTaxProfile(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateTaxProfile"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TaxProfiles';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteTaxProfile(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteTaxProfile"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TaxProfiles';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_TaxProfile

CREATE OR REPLACE TRIGGER "Delete_TaxProfile"
    BEFORE DELETE
    ON public."cl_TaxProfiles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TaxProfile

CREATE OR REPLACE TRIGGER "Insert_TaxProfile"
    BEFORE INSERT
    ON public."cl_TaxProfiles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_TaxProfile

CREATE OR REPLACE TRIGGER "Update_TaxProfile"
    BEFORE UPDATE 
    ON public."cl_TaxProfiles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.TaxRelations

DROP TABLE IF EXISTS public."cl_TaxRelations";
CREATE TABLE public."cl_TaxRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"TaxAttributeID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_TaxAttributes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"TaxProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_TaxProfiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_TaxProfileRelation" UNIQUE ("AttributeID", "ProfileID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTaxRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTaxRelation"(
	IN _taxattributeid character varying(50),
	IN _taxprofileid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TaxRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L) ON CONFLICT ("AttributeID", "ProfileID") DO NOTHING;', _tablename, _id, _taxattributeid, _taxprofileid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TXR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteTaxRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteTaxRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TaxRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Update_TaxRelation

CREATE OR REPLACE TRIGGER "Update_TaxRelation"
	BEFORE UPDATE
	ON public."cl_TaxRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TaxRelation

CREATE OR REPLACE TRIGGER "Insert_TaxRelation"
    BEFORE INSERT
    ON public."cl_TaxRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_TaxRelation

CREATE OR REPLACE TRIGGER "Remove_TaxRelation"
	BEFORE DELETE
	ON public."cl_TaxRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_PartnerTaxRelations

DROP TABLE IF EXISTS public."cl_PartnerTaxRelations";
CREATE TABLE public."cl_PartnerTaxRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"TaxProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_TaxProfiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ReferenceID" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK (public."f_CheckReference"("ReferenceID", "AppID")), -- customers, Suppliers, Employees
	"AppID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Apps" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertPartnerTaxRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertPartnerTaxRelation"(
	IN _taxprofileid character varying(50),
	IN _partnerid character varying(50),
	IN _appid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_PartnerTaxRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L);', _tablename, _id, _taxprofileid, _partnerid, appid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PXR');
END;
$BODY$;

-- PROCEDURE: public.p_DeletePartnerTaxRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeletePartnerTaxRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_PartnerTaxRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Update_PartnerTaxRelation

CREATE OR REPLACE TRIGGER "Update_PartnerTaxRelation"
	BEFORE UPDATE
	ON public."cl_PartnerTaxRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_PartnerTaxRelation

CREATE OR REPLACE TRIGGER "Insert_PartnerTaxRelation"
    BEFORE INSERT
    ON public."cl_PartnerTaxRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_PartnerTaxRelation

CREATE OR REPLACE TRIGGER "Remove_PartnerTaxRelation"
	BEFORE DELETE
	ON public."cl_PartnerTaxRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_RemoveTrigger"();