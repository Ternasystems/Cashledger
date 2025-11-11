/* Profiling app */

/* Profiles */

-- Table: public.cl_Profiles

DROP TABLE IF EXISTS public."cl_Profiles";
CREATE TABLE public."cl_Profiles"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "FirstName" character varying(50) COLLATE pg_catalog."default",
    "LastName" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "MaidenName" character varying(50) COLLATE pg_catalog."default",
	"BirthDate" timestamp without time zone NOT NULL,
	"CountryID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Countries" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"CityID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Cities" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"StartDate" timestamp without time zone DEFAULT NOW(),
	"EndDate" timestamp without time zone,
	"Photo" text COLLATE pg_catalog."default",
	"IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
    CONSTRAINT "UQ_Profile" UNIQUE ("FirstName", "LastName")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertProfile(character varying, timestamp without time zone, character varying, character varying, text, text)

CREATE OR REPLACE PROCEDURE public."p_InsertProfile"(
	IN _lastname character varying(50),
	IN _birthdate timestamp without time zone,
	IN _countryid character varying(50),
	IN _cityid character varying(50),
	IN _firstname character varying DEFAULT NULL::character varying(50),
	IN _maidenname character varying DEFAULT NULL::character varying(50),
	IN _photo text DEFAULT NULL::text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Profiles'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, %L, %L, NOW(), NULL, %L, NULL, %L);', _tablename, _id, _firstname, _lastname, _maidenname, _birthdate, _countryid, _cityid, _photo,
	_description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PRL');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateProfile(character varying, character varying, timestamp without time zone, character varying, character varying, text, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateProfile"(
	IN _id character varying(50),
	IN _lastname character varying(50),
	IN _birthdate timestamp without time zone,
	IN _countryid character varying(50),
	IN _cityid character varying(50),
	IN _maidenname character varying DEFAULT NULL::character varying(50),
	IN _firstname character varying DEFAULT NULL::character varying(50),
	IN _photo text DEFAULT NULL::text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Profiles';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "FirstName" = %L, "LastName" = %L, "MaidenName" = %L, "BirthDate" = %L, "CountryID" = %L, "CityID" = %L, "Photo" = %L, "Description" = %L WHERE "ID" = %L AND
	"EndDate" IS NULL;', _tablename, _firstname, _lastname, _maidenname, _birthdate, _countryid, _cityid, _photo, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteProfile(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteProfile"(IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Profiles';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L AND "EndDate" IS NULL;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DisableProfile(character varying)

CREATE OR REPLACE PROCEDURE public."p_DisableProfile"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Profiles';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "EndDate" = NOW() WHERE "ID" = %L AND "EndDate" IS NULL;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Profile

CREATE OR REPLACE TRIGGER "Delete_Profile"
    BEFORE DELETE
    ON public."cl_Profiles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Profile

CREATE OR REPLACE TRIGGER "Insert_Profile"
    BEFORE INSERT 
    ON public."cl_Profiles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Profile

CREATE OR REPLACE TRIGGER "Update_Profile"
    BEFORE UPDATE 
    ON public."cl_Profiles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();