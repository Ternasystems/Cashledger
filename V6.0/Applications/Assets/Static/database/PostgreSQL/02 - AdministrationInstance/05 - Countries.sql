/* Administration app */

/* Continents, Countries, Cities */

-- Table: public.cl_Continents

CREATE TABLE IF NOT EXISTS public."cl_Continents"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertContinent(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertContinent"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Continents'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CTN');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateContinent(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateContinent"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Continents';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteContinent(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteContinent"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Continents';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Continent

CREATE OR REPLACE TRIGGER "Delete_Continent"
    BEFORE DELETE
    ON public."cl_Continents"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Continent

CREATE OR REPLACE TRIGGER "Insert_Continent"
    BEFORE INSERT 
    ON public."cl_Continents"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Continent

CREATE OR REPLACE TRIGGER "Update_Continent"
	BEFORE UPDATE
	ON public."cl_Continents"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Countries

CREATE TABLE IF NOT EXISTS public."cl_Countries"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer NOT NULL,
	"ISO2" character(2) UNIQUE NOT NULL,
	"ISO3" character(3) UNIQUE NOT NULL,
    "ContinentID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Continents" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "Name" text COLLATE pg_catalog."default" NOT NULL,
    "Flag" text COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertCountry(integer, character, character, character varying, text, text, text)

CREATE OR REPLACE PROCEDURE public."p_InsertCountry"(
	IN _code integer,
	IN _iso2 character(2),
	IN _iso3 character(3),
	IN _continent character varying(50),
	IN _name text,
	IN _flag text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Countries'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _code, _iso2, _iso3, _continent, _name, _flag, NULL, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CTY');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateCountry(character varying, integer, character, character, character varying, text, text, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateCountry"(
	IN _id character varying(50),
	IN _code integer,
	IN _iso2 character(2),
	IN _iso3 character(3),
	IN _continent character varying(50),
	IN _name text,
	IN _flag text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Countries';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Code" = %L, "ISO2" = %L, "ISO3" = %L, "ContinentID" = %L, "Name" = %L, "Flag" = %L, "Description" = %L WHERE "ID" = %L;',
		_tablename, _code, _iso2, _iso3, _continent, _name, _flag, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteCountry(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteCountry"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Countries';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Country

CREATE OR REPLACE TRIGGER "Delete_Country"
    BEFORE DELETE
    ON public."cl_Countries"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Country

CREATE OR REPLACE TRIGGER "Insert_Country"
    BEFORE INSERT 
    ON public."cl_Countries"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Country

CREATE OR REPLACE TRIGGER "Update_Country"
	BEFORE UPDATE
	ON public."cl_Countries"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Cities

CREATE TABLE IF NOT EXISTS public."cl_Cities"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"Code" integer UNIQUE NOT NULL,
	"CountryID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Countries" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"Name" text COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertCity(character varying, text, text)

CREATE OR REPLACE PROCEDURE public."p_InsertCity"(
	IN _country character varying(50),
	IN _name text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Cities'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, NULL, %L);', _tablename, _id, _code, _country, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CIT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateCity(character varying, character varying, text, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateCity"(
	IN _id character varying(50),
	IN _country character varying(50),
	IN _name text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Cities';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "CountryID" = %L, "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _country, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteCity(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteCity"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Cities';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_City

CREATE OR REPLACE TRIGGER "Delete_City"
    BEFORE DELETE
    ON public."cl_Cities"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_City

CREATE OR REPLACE TRIGGER "Insert_City"
    BEFORE INSERT 
    ON public."cl_Cities"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_City

CREATE OR REPLACE TRIGGER "Update_City"
	BEFORE UPDATE
	ON public."cl_Cities"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_UpdateTrigger"();