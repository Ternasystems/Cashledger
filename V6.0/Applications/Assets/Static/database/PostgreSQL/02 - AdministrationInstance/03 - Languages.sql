/* Administration app */

/* Languages, LanguageRelations */

-- Table: public.cl_Languages

CREATE TABLE IF NOT EXISTS public."cl_Languages"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"Label" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertLanguage(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertLanguage"(
	IN _label character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Languages'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _label, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'LNG');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateLanguage(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateLanguage"(
	IN _id character varying(50),
	IN _label character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Languages';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Label" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _label, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteLanguage(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteLanguage"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Languages';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Language

CREATE OR REPLACE TRIGGER "Delete_Language"
    BEFORE DELETE
    ON public."cl_Languages"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Language

CREATE OR REPLACE TRIGGER "Insert_Language"
    BEFORE INSERT 
    ON public."cl_Languages"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Language

CREATE OR REPLACE TRIGGER "Update_Language"
	BEFORE UPDATE
	ON public."cl_Languages"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.LanguageRelations

CREATE TABLE IF NOT EXISTS public."cl_LanguageRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" NOT NULL PRIMARY KEY,
	"LangID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Languages" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ReferenceID" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"Label" text COLLATE pg_catalog."default" NOT NULL,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_LanguageRelation" UNIQUE ("ReferenceID", "LangID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertLanguageRelation(character varying, character varying, text, text)

CREATE OR REPLACE PROCEDURE public."p_InsertLanguageRelation"(
	IN _langid character varying(50),
	IN _referenceid character varying(50),
	IN _label text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_LanguageRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L);', _tablename, _id, _langid, _referenceid, _label, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'LGR');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateLanguageRelation(character varying, text, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateLanguageRelation"(
	IN _id character varying(50),
	IN _label text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_LanguageRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Label" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _label, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteLanguageRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteLanguageRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_LanguageRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_LanguageRelation

CREATE OR REPLACE TRIGGER "Delete_LanguageRelation"
    BEFORE DELETE
    ON public."cl_LanguageRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_LanguageRelation

CREATE OR REPLACE TRIGGER "Insert_LanguageRelation"
    BEFORE INSERT 
    ON public."cl_LanguageRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_LanguageRelation

CREATE OR REPLACE TRIGGER "Update_LanguageRelation"
	BEFORE UPDATE
	ON public."cl_LanguageRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Insert Languages

CALL public."p_InsertLanguage"('US', 'English (US)');
CALL public."p_InsertLanguage"('GB', 'English (GB)');
CALL public."p_InsertLanguage"('FR', 'Français');
CALL public."p_InsertLanguage"('ES', 'Español');
CALL public."p_InsertLanguage"('AR', 'عربي');