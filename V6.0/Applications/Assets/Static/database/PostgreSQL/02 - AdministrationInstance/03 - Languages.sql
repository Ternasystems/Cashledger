/* Administration app */

/* Languages, LanguageRelations */

-- Table: public.cl_Languages

DROP TABLE IF EXISTS public."cl_Languages";
CREATE TABLE public."cl_Languages"
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

-- Table: public.ReferenceTables

DROP TABLE IF EXISTS public."cl_ReferenceTables";
CREATE TABLE public."cl_ReferenceTables"
(
	"ID" character varying(50) COLLATE pg_catalog."default" NOT NULL PRIMARY KEY,
	"TableName" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertReferenceTable(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertReferenceTable"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ReferenceTables'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, NULL, %L);', _tablename, _id, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'RFT');
END;
$BODY$;

-- Trigger: Delete_ReferenceTable

CREATE OR REPLACE TRIGGER "Delete_ReferenceTable"
    BEFORE DELETE OR UPDATE
    ON public."cl_ReferenceTables"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_ReferenceTable

CREATE OR REPLACE TRIGGER "Insert_ReferenceTable"
    BEFORE INSERT 
    ON public."cl_ReferenceTables"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Table: public.ReferenceRelations

DROP TABLE IF EXISTS public."cl_ReferenceRelations";
CREATE TABLE public."cl_ReferenceRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" NOT NULL PRIMARY KEY,
	"ReferenceID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_ReferenceTables" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"AppID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Apps" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertReferenceRelation(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertReferenceRelation"(
	IN _referenceid character varying(50),
	IN _appid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ReferenceRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _referenceid, _appid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'RFR');
END;
$BODY$;

-- Trigger: Delete_ReferenceRelation

CREATE OR REPLACE TRIGGER "Delete_ReferenceRelation"
    BEFORE DELETE OR UPDATE
    ON public."cl_ReferenceRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_ReferenceRelation

CREATE OR REPLACE TRIGGER "Insert_ReferenceRelation"
    BEFORE INSERT 
    ON public."cl_ReferenceRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- FUNCTION: public.t_CheckReference(character varying)

CREATE OR REPLACE FUNCTION public."f_CheckReference"(
    _id character varying(50),
    _app_id character varying(50) DEFAULT NULL
)
    RETURNS boolean
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE
    _table_name VARCHAR(50);
    _sql TEXT;
    _exists BOOLEAN;
BEGIN
    -- Loop through reference tables, optionally filtered by application
    FOR _table_name IN 
        SELECT DISTINCT rt."TableName" 
        FROM public."cl_ReferenceTables" rt
        WHERE rt."IsActive" IS NOT NULL
          AND (
              _app_id IS NULL 
              OR EXISTS (
                  SELECT 1 
                  FROM public."cl_ReferenceRelations" rr
                  WHERE rr."ReferenceID" = rt."ID"
                    AND rr."AppID" = _app_id
                    AND rr."IsActive" IS NOT NULL
              )
          )
        ORDER BY rt."TableName"
    LOOP
        -- Build dynamic query
        _sql := FORMAT('SELECT EXISTS(SELECT 1 FROM public.%I WHERE "ID" = $1)', _table_name);
        
        -- Execute and check if ID exists
        EXECUTE _sql INTO _exists USING _id;
        
        IF _exists THEN
            RETURN TRUE;
        END IF;
    END LOOP;
    
    RETURN FALSE;
END;
$BODY$;

-- Table: public.LanguageRelations

DROP TABLE IF EXISTS public."cl_LanguageRelations";
CREATE TABLE public."cl_LanguageRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" NOT NULL PRIMARY KEY,
	"LangID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Languages" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ReferenceID" character varying(50) COLLATE pg_catalog."default" NOT NULL CHECK (public."f_CheckReference"("ReferenceID")),
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

-- Insert References

CALL public."p_InsertReferenceTable"('cl_Languages');