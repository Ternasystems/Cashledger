/* Administration app */

/* AppCategories, Apps, AppRelations */

-- Table: public.cl_AppCategories

DROP TABLE IF EXISTS public."cl_AppCategories";
CREATE TABLE public."cl_AppCategories"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertAppCategory(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertAppCategory"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_AppCategories'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'ACT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateAppCategory(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateAppCategory"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_AppCategories';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteAppCategory(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteAppCategory"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_AppCategories';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_AppCategory

CREATE OR REPLACE TRIGGER "Delete_AppCategory"
    BEFORE DELETE
    ON public."cl_AppCategories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_AppCategory

CREATE OR REPLACE TRIGGER "Insert_AppCategory"
    BEFORE INSERT 
    ON public."cl_AppCategories"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_AppCategory

CREATE OR REPLACE TRIGGER "Update_AppCategory"
	BEFORE UPDATE
	ON public."cl_AppCategories"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Apps

DROP TABLE IF EXISTS public."cl_Apps";
CREATE TABLE public."cl_Apps"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertApp(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertApp"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Apps'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'APP');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateApp(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateApp"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Apps';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteApp(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteApp"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Apps';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_App

CREATE OR REPLACE TRIGGER "Delete_App"
    BEFORE DELETE
    ON public."cl_Apps"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_App

CREATE OR REPLACE TRIGGER "Insert_App"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Apps"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_App

CREATE OR REPLACE TRIGGER "Update_App"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Apps"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_AppRelations

DROP TABLE IF EXISTS public."cl_AppRelations";
CREATE TABLE public."cl_AppRelations"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "AppID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Apps" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "AppCategoryID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_AppCategories" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
    CONSTRAINT "UQ_AppRelation" UNIQUE ("AppID", "AppCategoryID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertAppRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertAppRelation"(
	IN _appid character varying(50),
	IN _categoryid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_AppRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _appid, _categoryid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'APR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteAppRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteAppRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_AppRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_AppRelation

CREATE OR REPLACE TRIGGER "Update_AppRelation"
	BEFORE UPDATE
	ON public."cl_AppRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_AppRelation

CREATE OR REPLACE TRIGGER "Insert_AppRelation"
    BEFORE INSERT
    ON public."cl_AppRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_AppRelation

CREATE OR REPLACE TRIGGER "Remove_AppRelation"
	BEFORE DELETE
	ON public."cl_AppRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Insert References

CALL public."p_InsertReferenceTable"('cl_AppCategories');
CALL public."p_InsertReferenceTable"('cl_Apps');