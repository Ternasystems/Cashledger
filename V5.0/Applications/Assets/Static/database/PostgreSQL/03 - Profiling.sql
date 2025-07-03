/* Profiling app */

-- Table: public.cl_Profiles

CREATE TABLE IF NOT EXISTS public."cl_Profiles"
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
	
-- Table: public.cl_Titles

CREATE TABLE IF NOT EXISTS public."cl_Titles"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTitle(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTitle"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Titles'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TTL');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateTitle(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateTitle"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Titles';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteTitle(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteTitle"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Titles';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Title

CREATE OR REPLACE TRIGGER "Delete_Title"
    BEFORE DELETE
    ON public."cl_Titles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Title

CREATE OR REPLACE TRIGGER "Insert_Title"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Titles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Title

CREATE OR REPLACE TRIGGER "Update_Title"
    BEFORE INSERT OR UPDATE 
    ON public."cl_Titles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_TitleRelations

CREATE TABLE IF NOT EXISTS public."cl_TitleRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"TitleID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Titles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_TitleRelation" UNIQUE ("TitleID", "ProfileID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertTitleRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTitleRelation"(
	IN _titleid character varying(50),
	IN _profileid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TitleRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _titleid, _profileid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'TTR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteTitleRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteTitleRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_TitleRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_TitleRelation

CREATE OR REPLACE TRIGGER "Update_TitleRelation"
	BEFORE UPDATE
	ON public."cl_TitleRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_TitleRelation

CREATE OR REPLACE TRIGGER "Insert_TitleRelation"
    BEFORE INSERT
    ON public."cl_TitleRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_TitleRelation

CREATE OR REPLACE TRIGGER "Remove_TitleRelation"
    BEFORE DELETE
    ON public."cl_TitleRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();
	
-- Table: public.cl_Statuses

CREATE TABLE IF NOT EXISTS public."cl_Statuses"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertStatus(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertStatus"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Statuses'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'STS');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateStatus(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateStatus"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Statuses';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteStatus(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteStatus"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Statuses';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Status

CREATE OR REPLACE TRIGGER "Delete_Status"
    BEFORE DELETE
    ON public."cl_Statuses"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Status

CREATE OR REPLACE TRIGGER "Insert_Status"
    BEFORE INSERT 
    ON public."cl_Statuses"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Status

CREATE OR REPLACE TRIGGER "Update_Status"
    BEFORE UPDATE 
    ON public."cl_Statuses"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_StatusRelations

CREATE TABLE IF NOT EXISTS public."cl_StatusRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"StatusID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Statuses" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_StatusRelation" UNIQUE ("StatusID", "ProfileID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertStatusRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertStatusRelation"(
	IN _statusid character varying(50),
	IN _profileid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_StatusRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _statusid, _profileid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'STR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteStatusRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteStatusRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_StatusRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_StatusRelation

CREATE OR REPLACE TRIGGER "Update_StatusRelation"
	BEFORE UPDATE
	ON public."cl_StatusRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_StatusRelation

CREATE OR REPLACE TRIGGER "Insert_StatusRelation"
    BEFORE INSERT
    ON public."cl_StatusRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_StatusRelation

CREATE OR REPLACE TRIGGER "Remove_StatusRelation"
    BEFORE DELETE
    ON public."cl_StatusRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_Genders

-- DROP TABLE IF EXISTS public."cl_Genders";

CREATE TABLE IF NOT EXISTS public."cl_Genders"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertGender(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertGender"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Genders'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'GND');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateGender(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateGender"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Genders';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteGender(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteGender"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Genders';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Gender

CREATE OR REPLACE TRIGGER "Delete_Gender"
    BEFORE DELETE
    ON public."cl_Genders"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Gender

CREATE OR REPLACE TRIGGER "Insert_Gender"
    BEFORE INSERT
    ON public."cl_Genders"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Gender

CREATE OR REPLACE TRIGGER "Update_Gender"
    BEFORE UPDATE 
    ON public."cl_Genders"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_GenderRelations

CREATE TABLE IF NOT EXISTS public."cl_GenderRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"GenderID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Genders" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_GenderRelation" UNIQUE ("GenderID", "ProfileID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertGenderRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertGenderRelation"(
	IN _genderid character varying(50),
	IN _profileid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_GenderRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _genderid, _profileid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'GNR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteGenderRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteGenderRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_GenderRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_GenderRelation

CREATE OR REPLACE TRIGGER "Update_GenderRelation"
	BEFORE UPDATE
	ON public."cl_GenderRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_GenderRelation

CREATE OR REPLACE TRIGGER "Insert_GenderRelation"
    BEFORE INSERT
    ON public."cl_GenderRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_GenderRelation

CREATE OR REPLACE TRIGGER "Remove_GenderRelation"
    BEFORE DELETE
    ON public."cl_GenderRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_Civilities

CREATE TABLE IF NOT EXISTS public."cl_Civilities"
(
    "ID" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "Code" integer NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
    CONSTRAINT "PK_Civility" PRIMARY KEY ("ID"),
    CONSTRAINT "UQ_Civility" UNIQUE ("Code")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertCivility(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertCivility"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Civilities'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CIV');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateCivility(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateCivility"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Civilities';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteCivility(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteCivility"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Civilities';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Civility

CREATE OR REPLACE TRIGGER "Delete_Civility"
    BEFORE DELETE
    ON public."cl_Civilities"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Civility

CREATE OR REPLACE TRIGGER "Insert_Civility"
    BEFORE INSERT
    ON public."cl_Civilities"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Civility

CREATE OR REPLACE TRIGGER "Update_Civility"
    BEFORE UPDATE 
    ON public."cl_Civilities"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_CivilityRelations

CREATE TABLE IF NOT EXISTS public."cl_CivilityRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"CivilityID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Civilities" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_CivilityRelation" UNIQUE ("CivilityID", "ProfileID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertCivilityRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertCivilityRelation"(
	IN _civilityid character varying(50),
	IN _profileid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_CivilityRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _civilityid, _profileid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CVR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteCivilityRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteCivilityRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_CivilityRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_CivilityRelation

CREATE OR REPLACE TRIGGER "Update_CivilityRelation"
	BEFORE UPDATE
	ON public."cl_CivilityRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_CivilityRelation

CREATE OR REPLACE TRIGGER "Insert_CivilityRelation"
    BEFORE INSERT
    ON public."cl_CivilityRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_CivilityRelation

CREATE OR REPLACE TRIGGER "Remove_CivilityRelation"
    BEFORE DELETE
    ON public."cl_CivilityRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_Occupations

CREATE TABLE IF NOT EXISTS public."cl_Occupations"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertOccupation(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertOccupation"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Occupations'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'OCP');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateOccupation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateOccupation"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Occupations';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteOccupation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteOccupation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Occupations';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Occupation

CREATE OR REPLACE TRIGGER "Delete_Occupation"
    BEFORE DELETE
    ON public."cl_Occupations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Occupation

CREATE OR REPLACE TRIGGER "Insert_Occupation"
    BEFORE INSERT
    ON public."cl_Occupations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Occupation

CREATE OR REPLACE TRIGGER "Update_Occupation"
    BEFORE UPDATE 
    ON public."cl_Occupations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_OccupationRelations

CREATE TABLE IF NOT EXISTS public."cl_OccupationRelations"
(
	"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"OccupationID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Occupations" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
	"IsActive" timestamp without time zone,
	"Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_OccupationRelation" UNIQUE ("OccupationID", "ProfileID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertOccupationRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertOccupationRelation"(
	IN _occupationid character varying(50),
	IN _profileid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_OccupationRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _occupationid, _profileid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'OCR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteOccupationRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteOccupationRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_OccupationRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_OccupationRelation

CREATE OR REPLACE TRIGGER "Update_OccupationRelation"
	BEFORE UPDATE
	ON public."cl_OccupationRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_OccupationRelation

CREATE OR REPLACE TRIGGER "Insert_OccupationRelation"
    BEFORE INSERT
    ON public."cl_OccupationRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_OccupationRelation

CREATE OR REPLACE TRIGGER "Remove_OccupationRelation"
    BEFORE DELETE
    ON public."cl_OccupationRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_ContactTypes

CREATE TABLE IF NOT EXISTS public."cl_ContactTypes"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertContactType(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertContactType"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ContactTypes'; _id character varying(50) := '%s'; _code integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("Code"), 0) + 1 FROM public.%I', _tablename) INTO _code;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CTT');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateContactType(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateContactType"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ContactTypes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteContactType(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteContactType"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ContactTypes';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_ContactType

CREATE OR REPLACE TRIGGER "Delete_ContactType"
    BEFORE DELETE
    ON public."cl_ContactTypes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_ContactType

CREATE OR REPLACE TRIGGER "Insert_ContactType"
    BEFORE INSERT
    ON public."cl_ContactTypes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_ContactType

CREATE OR REPLACE TRIGGER "Update_ContactType"
    BEFORE UPDATE 
    ON public."cl_ContactTypes"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Contacts

CREATE TABLE IF NOT EXISTS public."cl_Contacts"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "ContactTypeID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_ContactTypes" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "ContactNo" integer NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
	CONSTRAINT "UQ_Contact" UNIQUE ("ProfileID", "ContactNo")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertContact(character varying, character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertContact"(
	IN _contacttypeid character varying(50),
	IN _profileid character varying(50),
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Contacts'; _id character varying(50) := '%s'; _no integer;
BEGIN
	-- Set the Code
	EXECUTE FORMAT('SELECT COALESCE(MAX("ContactNo"), 0) + 1 FROM public.%I', _tablename) INTO _no;
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _contacttypeid, _profileid, _no, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CTC');
END;
$BODY$;

-- PROCEDURE: public.p_UpdateContact(character varying, character varying, character varying, character varying, integer, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateContact"(
	IN _id character varying(50),
	IN _contacttype character varying(50),
	IN _profileid character varying(50),
	IN _name character varying(50),
	IN _contactno integer DEFAULT 0,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Contacts'; _no integer; _tempno integer;
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "ContactTypeID" = %L, "ProfileID" = %L, "Name" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _contacttype, _profileid, _name, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
	-- Check contact no
	EXECUTE FORMAT('SELECT "ContactNo" FROM public.%I WHERE "ID" = %L;', _tablename, _id) INTO _no;
	IF _contactno != 0 AND _contactno != _no THEN
		-- Use a temporary value to swap ContactNo
        _tempno := -1;  -- Assuming -1 is not a valid ContactNo
        
        -- First, set the ContactNo to a temporary value
        _sql := FORMAT('UPDATE public.%I SET "ContactNo" = %L WHERE "ID" = %L;', _tablename, _tempno, _id);
        CALL public."p_Query"(_sql);
        
        -- Then, update the other record
        _sql := FORMAT('UPDATE public.%I SET "ContactNo" = %L WHERE "ContactNo" = %L;', _tablename, _no, _contactno);
        CALL public."p_Query"(_sql);
        
        -- Finally, set the ContactNo to the desired value
        _sql := FORMAT('UPDATE public.%I SET "ContactNo" = %L WHERE "ID" = %L;', _tablename, _contactno, _id);
        CALL public."p_Query"(_sql);
	END IF;
END;
$BODY$;

-- PROCEDURE: public.p_DeleteContact(character varying, boolean)

CREATE OR REPLACE PROCEDURE public."p_DeleteContact"(IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Contacts';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger Delete_Contact
CREATE OR REPLACE TRIGGER "Delete_Contact"
	BEFORE DELETE
	ON public."cl_Contacts"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Contact

CREATE OR REPLACE TRIGGER "Insert_Contact"
    BEFORE INSERT 
    ON public."cl_Contacts"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Contact

CREATE OR REPLACE TRIGGER "Update_Contact"
    BEFORE UPDATE 
    ON public."cl_Contacts"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_ContactRelations

CREATE TABLE IF NOT EXISTS public."cl_ContactRelations"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
	"LangID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Languages" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "ContactID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Contacts" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "Contact" text COLLATE pg_catalog."default" NOT NULL,
    "Photo" text COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" character varying(50) COLLATE pg_catalog."default",
    CONSTRAINT "UQ_ContactRelation" UNIQUE ("LangID", "ContactID", "Contact")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertContactRelation(character varying, character varying, text, text, text, text)

CREATE OR REPLACE PROCEDURE public."p_InsertContactRelation"(
	IN _langid character varying(50),
	IN _contactid character varying(50),
	IN _contact text,
	IN _photo text,
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ContactRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, %L, %L, NULL, %L);', _tablename, _id, _langid, _contactid, _contact, _photo, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'CTR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteContactRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteContactRelation"(
	IN _id character varying(50),
	IN _deactivate boolean DEFAULT true)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_ContactRelations';
BEGIN
	-- Format sql
	IF _deactivate = TRUE THEN
		_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	ELSE
		_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L;', _tablename, _id);
	END IF;
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Update_ContactRelation

CREATE OR REPLACE TRIGGER "Update_ContactRelation"
	BEFORE UPDATE
	ON public."cl_ContactRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_ContactRelation

CREATE OR REPLACE TRIGGER "Insert_ContactRelation"
    BEFORE INSERT
    ON public."cl_ContactRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_ContactRelation

CREATE OR REPLACE TRIGGER "Remove_ContactRelation"
    BEFORE DELETE 
    ON public."cl_ContactRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_Credentials

CREATE TABLE IF NOT EXISTS public."cl_Credentials"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "ProfileID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Profiles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "UserName" character varying(50) COLLATE pg_catalog."default" UNIQUE NOT NULL CHECK ("UserName" ~* '^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$'),
    "UserPassword" bytea NOT NULL,
    "StartDate" timestamp without time zone NOT NULL,
    "EndDate" timestamp without time zone,
    "SessionID" text COLLATE pg_catalog."default",
    "ConnectionStatus" boolean NOT NULL,
	"LoginStatus" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"CurrentThread" boolean NOT NULL,
	"Threads" integer NOT NULL CHECK ("Threads" >= 0),
	"IP" character varying(50) COLLATE pg_catalog."default",
	"Action" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- FUNCTION: public."t_LogCredential"()

CREATE OR REPLACE FUNCTION public."t_LogCredential"()
	RETURNS trigger
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE NOT LEAKPROOF
AS $BODY$
DECLARE _threads integer;
BEGIN
	IF TG_OP = 'INSERT' THEN
		CALL public."p_InsertAudit"(TG_OP::character varying(50), TG_TABLE_NAME::character varying(50), NEW."ID"::character varying(50), row_to_json(NEW)::jsonb);		
	ELSIF TG_OP = 'UPDATE' THEN
		CALL public."p_InsertAudit"(
			CASE
				WHEN NEW."EndDate" IS NOT NULL THEN 'DISABLED'
				WHEN NEW."IsActive" IS NOT NULL THEN 'DEACTIVATE'
				ELSE TG_OP::character varying(50)
			END,
			TG_TABLE_NAME::character varying(50),
			OLD."ID"::character varying(50),
			json_build_object('before: ', row_to_json(OLD), ' after: ', row_to_json(NEW))::jsonb
		);
	ELSIF TG_OP = 'DELETE' THEN
		CALL public."p_InsertAudit"(TG_OP::character varying(50), TG_TABLE_NAME::character varying(50), OLD."ID"::character varying(50), row_to_json(OLD)::jsonb);
	END IF;
	RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
END;
$BODY$;

-- FUNCTION: public.t_ReleaseThread()

CREATE OR REPLACE FUNCTION public."t_ReleaseThread"()
	RETURNS trigger
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE NOT LEAKPROOF
AS $BODY$
DECLARE _threads integer; _threaded boolean; _processed boolean;
BEGIN
	IF TG_TABLE_NAME = 'cl_Credentials' THEN RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END; END IF;
	--
	IF TG_TABLE_NAME = 'cl_Parameters' THEN
		IF public."f_Auditable"(CASE WHEN TG_OP = 'INSERT' THEN NEW."ID" ELSE OLD."ID" END) = FALSE THEN
			RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
		END IF;
	END IF;
	--
	SELECT "Threads" INTO _threads FROM public."cl_Credentials" WHERE "CurrentThread" = TRUE;
	_threads := _threads - 1;
	
	-- Set CurrentThread parameter
	SELECT public."f_CurrentThread"() INTO _threaded;
	IF _threaded = FALSE THEN CALL public."p_CurrentThread"(TRUE); END IF;
	
	-- Set IsProc parameter
	SELECT public."f_IsProc"() INTO _processed;
    IF _processed = FALSE THEN CALL public."p_IsProc"(TRUE); END IF;

	-- Update the credentials table
	BEGIN
		IF _threads = 0 THEN
			UPDATE public."cl_Credentials" SET "Threads" = _threads, "CurrentThread" = FALSE, "Action" = 'UPDATE' WHERE "CurrentThread" = TRUE;
		ELSE
			UPDATE public."cl_Credentials" SET "Threads" = _threads, "Action" = 'UPDATE' WHERE "CurrentThread" = TRUE;
		END IF;
		--
		RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
	--
	EXCEPTION WHEN OTHERS THEN
		-- Reset IsProc parameter
	    CALL public."p_IsProc"(FALSE);
		
		-- Reset CurrentThread parameter
		CALL public."p_CurrentThread"(FALSE);
		RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
	END;
	-- Reset IsProc parameter
    IF _processed = FALSE THEN CALL public."p_IsProc"(FALSE); END IF;
	
	-- Reset CurrentThread parameter
	IF _threaded = FALSE THEN CALL public."p_CurrentThread"(FALSE); END IF;
	--
	RETURN CASE WHEN TG_OP = 'INSERT' THEN NEW ELSE OLD END;
END;
$BODY$;

-- FUNCTION: public.f_CheckLoginStatus(character varying)

CREATE OR REPLACE FUNCTION public."f_CheckLoginStatus"(_id character varying(50))
	RETURNS character varying
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE _status character varying(50);
BEGIN
	SELECT "LoginStatus" INTO _status FROM public."cl_Credentials" WHERE "ID" = _id;
	RETURN _status;
END;
$BODY$;

-- PROCEDURE: public.p_LoginStatus(character varying, character varying, character varying)

CREATE OR REPLACE PROCEDURE public."p_LoginStatus"(
	IN _id character varying(50),
	IN _status character varying(50),
	IN _ip character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "LoginStatus" = %L, "IP" = %L, "Action" = ''LOGIN_ATTEMPT'' WHERE "ID" = %L;', _tablename, _status, _ip, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- FUNCTION: public.f_CheckCurrentThread(character varying)

CREATE OR REPLACE FUNCTION public."f_CheckCurrentThread"()
    RETURNS boolean
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
BEGIN
	IF EXISTS(SELECT 1 FROM public."cl_Credentials" WHERE "CurrentThread" = TRUE) THEN
		RETURN TRUE;
	END IF;
	RETURN FALSE;
END;
$BODY$;

-- PROCEDURE: public.p_CurrentThread(character varying, boolean)

CREATE OR REPLACE PROCEDURE public."p_SetCurrentThread"(OUT _isThreaded boolean, IN _id character varying(50), IN _threads integer)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials'; _threaded boolean;
BEGIN
	LOOP
		SELECT public."f_CheckCurrentThread"() INTO _threaded;
		EXIT WHEN _threaded = FALSE;
	END LOOP;
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "CurrentThread" = TRUE, "Threads" = %L, "Action" = ''UPDATE'' WHERE "ID" = %L;', _tablename, _threads, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
	--
	_isThreaded := public."f_CheckCurrentThread"();
END;
$BODY$;

-- FUNCTION: public.f_CheckConnectionStatus(character varying)

CREATE OR REPLACE FUNCTION public."f_CheckConnectionStatus"(_id character varying(50))
    RETURNS boolean
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
BEGIN
	IF EXISTS(SELECT 1 FROM public."cl_Credentials" WHERE "ID" = _id AND "ConnectionStatus" = TRUE) THEN
		RETURN TRUE;
	END IF;
	RETURN FALSE;
END;
$BODY$;

-- PROCEDURE: public.p_ConnectionStatus(character varying, boolean, text)

CREATE OR REPLACE PROCEDURE public."p_ConnectionStatus"(
	IN _id character varying(50),
	IN _connected boolean,
	IN _sessionid text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials';
BEGIN
	-- Format sql
	_sql := FORMAT('
		UPDATE public.%I SET "SessionID" = CASE WHEN %L THEN %L ELSE NULL END, "ConnectionStatus" = %L, "Action" = ''UPDATE'' WHERE "ID" = %L;', _tablename, _connected, _sessionid, _connected, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- FUNCTION: public.f_CheckCredential(character varying, character varying)

CREATE OR REPLACE FUNCTION public."f_CheckCredential"(
	_username character varying(50),
	_userpassword character varying(50),
	_ip character varying(50))
    RETURNS SETOF "cl_Credentials" 
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
    ROWS 1000
AS $BODY$
DECLARE _id character varying(50);
BEGIN
	-- Check successful
	RETURN QUERY SELECT * FROM public."cl_Credentials" WHERE "UserName" = _username AND "UserPassword" = DIGEST(_userpassword, 'sha256');
	
	-- Check failed
	IF NOT FOUND THEN
		IF EXISTS(SELECT 1 FROM public."cl_Credentials" WHERE "UserName" = _username) THEN
			SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "UserName" = _username;
		ELSE
			SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "UserName" = 'unkown@unkown.com';
		END IF;
		CALL public."p_LoginStatus"(_id, 'LOGIN_FAILED', _ip);
	END IF;
END;
$BODY$;

-- FUNCTION: public."p_ReleaseThread"()

CREATE OR REPLACE PROCEDURE public."p_ReleaseThread"()
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _threads integer;
BEGIN
	SELECT "Threads" INTO _threads FROM public."cl_Credentials" WHERE "CurrentThread" = TRUE;
	--
	_threads := _threads - 1;
	IF _threads = 0 THEN
		UPDATE public."cl_Credentials" SET "Threads" = _threads, "CurrentThread" = FALSE, "Action" = 'UPDATE' WHERE "CurrentThread" = TRUE;
	ELSE
		UPDATE public."cl_Credentials" SET "Threads" = _threads, "Action" = 'UPDATE' WHERE "CurrentThread" = TRUE;
	END IF;
END;
$BODY$;

-- PROCEDURE: public.p_InsertCredential(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertCredential"(
	OUT _pwd character varying(50),
	IN _profileid character varying(50),
	IN _username character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _id character varying(50);
BEGIN
	-- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);
	--
	BEGIN
		-- Create ID
		_id := public."f_CreateID"('CRD', 'cl_Credentials');
		-- Set password
		_pwd := public."f_PwdGenerator"();
		-- Insert data to Credentials table
		INSERT INTO public."cl_Credentials" VALUES (_id, _profileid, _username, DIGEST(_pwd, 'sha256'), NOW(), NULL, NULL, FALSE, 'LOGOUT', FALSE, 0, NULL, 'INSERT', NULL, _description);
		--
		CALL public."p_ReleaseThread"();
	--
	EXCEPTION WHEN OTHERS THEN
		-- Reset IsProc parameter
	    CALL public."p_IsProc"(FALSE);
		
		-- Reset CurrentThread parameter
		CALL public."p_CurrentThread"(FALSE);
		RETURN;
	END;
	-- Reset IsProc parameter
    CALL public."p_IsProc"(FALSE);
	
	-- Reset CurrentThread parameter
	CALL public."p_CurrentThread"(FALSE);
END;
$BODY$;

-- PROCEDURE: public.p_UpdateCredential(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_UpdateCredential"(
	IN _id character varying(50),
	IN _profileid character varying(50),
	IN _username character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "ProfileID" = %L, "UserName" = %L, "Action" = ''UPDATE'', "Description" = %L WHERE "ID" = %L;', _tablename, _profileid, _username, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
	--
	CALL public."p_ReleaseThread"();
END;
$BODY$;

-- PROCEDURE: public.p_UpdatePassword(character varying, character varying, character varying)

CREATE OR REPLACE PROCEDURE public."p_UpdatePassword"(
	IN _id character varying(50),
	IN _oldpwd character varying(50),
	IN _newpwd character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials';
BEGIN
	-- Format sql
	IF EXISTS(SELECT 1 FROM public."cl_Credentials" WHERE "ID" = _id AND "UserPassword" = DIGEST(_oldpwd, 'sha256')) AND _oldpwd != _newpwd THEN
		_sql := FORMAT('UPDATE public.%I SET "UserPassword" = DIGEST(%L, ''sha256''), "Action" = ''UPDATE'' WHERE "ID" = %L;', _tablename, _newpwd, _id);
	END IF;
	-- Execute sql
	CALL public."p_Query"(_sql);
	--
	CALL public."p_ReleaseThread"();
END;
$BODY$;

-- PROCEDURE: public.p_ResetPassword(character varying)

CREATE OR REPLACE PROCEDURE public."p_ResetPassword"(
	OUT _pwd character varying(50),
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
BEGIN
	-- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);
	--
	BEGIN
		-- Set password
		_pwd := public."f_PwdGenerator"();
		-- Update password from Credentials table
		UPDATE public."cl_Credentials" SET "UserPassword" = DIGEST(_pwd, 'sha256'), "Action" = 'UPDATE' WHERE "ID" = _id;
		--
		CALL public."p_ReleaseThread"();
	--
	EXCEPTION WHEN OTHERS THEN
		-- Reset IsProc parameter
	    CALL public."p_IsProc"(FALSE);
		
		-- Reset CurrentThread parameter
		CALL public."p_CurrentThread"(FALSE);
		RETURN;
	END;
	-- Reset IsProc parameter
    CALL public."p_IsProc"(FALSE);
	
	-- Reset CurrentThread parameter
	CALL public."p_CurrentThread"(FALSE);
END;
$BODY$;

-- PROCEDURE: public.p_DeleteCredential(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteCredential"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW(), "Action" = ''UPDATE'' WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
	--
	CALL public."p_ReleaseThread"();
END;
$BODY$;

-- PROCEDURE: public.p_DisableCredential(character varying)

CREATE OR REPLACE PROCEDURE public."p_DisableCredential"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Credentials';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "EndDate" = NOW(), "Action" = ''UPDATE'' WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
	--
	CALL public."p_ReleaseThread"();
END;
$BODY$;

-- Trigger: Delete_Credential

CREATE OR REPLACE TRIGGER "Delete_Credential"
    BEFORE DELETE
    ON public."cl_Credentials"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Credential

CREATE OR REPLACE TRIGGER "Insert_Credential"
    BEFORE INSERT
    ON public."cl_Credentials"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Credential

CREATE OR REPLACE TRIGGER "Update_Credential"
    BEFORE UPDATE 
    ON public."cl_Credentials"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Permissions

CREATE TABLE IF NOT EXISTS public."cl_Permissions"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" character(1) COLLATE pg_catalog."default" UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertPermission(character varying, character, text)

CREATE OR REPLACE PROCEDURE public."p_InsertPermission"(
	IN _name character varying(50),
	IN _code character(1),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Permissions'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _code, _name, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'PRM');
END;
$BODY$;

-- PROCEDURE: public.p_UpdatePermission(character varying, character varying, character, text)

CREATE OR REPLACE PROCEDURE public."p_UpdatePermission"(
	IN _id character varying(50),
	IN _name character varying(50),
	IN _code character(1),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Permissions';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "Name" = %L, "Code" = %L, "Description" = %L WHERE "ID" = %L;', _tablename, _name, _code, _description, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- PROCEDURE: public.p_DeletePermission(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeletePermission"(IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_Permissions';
BEGIN
	-- Format sql
	_sql := FORMAT('UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = %L;', _tablename, _id);
	-- Execute sql
	CALL public."p_Query"(_sql);
END;
$BODY$;

-- Trigger: Delete_Permission

CREATE OR REPLACE TRIGGER "Delete_Permission"
    BEFORE DELETE
    ON public."cl_Permissions"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Permission

CREATE OR REPLACE TRIGGER "Insert_Permission"
    BEFORE INSERT
    ON public."cl_Permissions"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Permission

CREATE OR REPLACE TRIGGER "Update_Permission"
    BEFORE UPDATE 
    ON public."cl_Permissions"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_Roles

CREATE TABLE IF NOT EXISTS public."cl_Roles"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "Code" integer UNIQUE NOT NULL,
    "Name" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default"
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertRole(character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertRole"(
	IN _name character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _id character varying(50); _code integer; _sql text; _tablename text; _procname text;
BEGIN
	-- Set CurrentThread parameter
	CALL public."p_CurrentThread"(TRUE);
	
	-- Set IsProc parameter
    CALL public."p_IsProc"(TRUE);
	--
	BEGIN
		-- Create ID
		_id := public."f_CreateID"('ROL', 'cl_Roles');
		-- Set the Code
		SELECT COALESCE(MAX("Code"), 0) + 1 INTO _code FROM public."cl_Roles";
		-- Insert data into cl_Roles
		INSERT INTO public."cl_Roles" VALUES (_id, _code, _name, NULL, _description);
		-- Create role table
		_tablename := 'cl_Role_' || _name;
		EXECUTE FORMAT('
			CREATE TABLE IF NOT EXISTS public.%I
			(
				"ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
				"RoleID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Roles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
				"Controller" character varying(50) COLLATE pg_catalog."default" NOT NULL,
				"Action" character varying(50) COLLATE pg_catalog."default" NOT NULL,
				"Permissions" character varying(50) COLLATE pg_catalog."default" NOT NULL,
				"IsActive" timestamp without time zone,
   				"Description" text COLLATE pg_catalog."default",
				CONSTRAINT %I UNIQUE ("RoleID", "Controller", "Action") 
			)
			TABLESPACE pg_default;
		', _tablename, 'PK_' || _name, 'UQ_' || _name, 'FK_' || _name);
		--
		_procname := 'p_InsertRole_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE PROCEDURE public.%I(
				IN _roleid character varying(50),
				IN _controller character varying(50),
				IN _action character varying(50),
				IN _permissions character varying(50),
				IN _description text DEFAULT NULL::text)
			LANGUAGE ''plpgsql''
			AS $$
			DECLARE _id character varying(50);
			BEGIN
				-- Set CurrentThread parameter
				CALL public."p_CurrentThread"(TRUE);
				
				-- Set IsProc parameter
			    CALL public."p_IsProc"(TRUE);
				--
				BEGIN
				-- Create ID
				_id := public."f_CreateID"(''PRR'', %L);
				-- Insert data into cl_Roles
				INSERT INTO public.%I VALUES (_id, _roleid, _controller, _action, _permissions, NULL, _description);
				--
				EXCEPTION WHEN OTHERS THEN
					-- Reset IsProc parameter
				    CALL public."p_IsProc"(FALSE);
					
					-- Reset CurrentThread parameter
					CALL public."p_CurrentThread"(FALSE);
					RETURN;
				END;
				-- Reset IsProc parameter
			    CALL public."p_IsProc"(FALSE);
				
				-- Reset CurrentThread parameter
				CALL public."p_CurrentThread"(FALSE);
			END;
			$$;
		', _procname, _tablename, _tablename);
		_procname := 'p_UpdateRole_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE PROCEDURE public.%I(
				IN _id character varying(50),
				IN _permissions character varying(50),
				IN _description text DEFAULT NULL::text)
			LANGUAGE ''plpgsql''
			AS $$
			BEGIN
				-- Set CurrentThread parameter
				CALL public."p_CurrentThread"(TRUE);
				
				-- Set IsProc parameter
			    CALL public."p_IsProc"(TRUE);
				--
				BEGIN
				-- Update data from cl_Roles table
				UPDATE public.%I SET "Permissions" = _permissions, "Description" = _description WHERE "ID" = _id;
				--
				EXCEPTION WHEN OTHERS THEN
					-- Reset IsProc parameter
				    CALL public."p_IsProc"(FALSE);
					
					-- Reset CurrentThread parameter
					CALL public."p_CurrentThread"(FALSE);
					RETURN;
				END;
				-- Reset IsProc parameter
			    CALL public."p_IsProc"(FALSE);
				
				-- Reset CurrentThread parameter
				CALL public."p_CurrentThread"(FALSE);
			END;
			$$;
		', _procname, _tablename);
		_procname := 'p_DeleteRole_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE PROCEDURE public.%I(IN _id character varying(50))
			LANGUAGE ''plpgsql''
			AS $$
			BEGIN
				-- Set CurrentThread parameter
				CALL public."p_CurrentThread"(TRUE);
				
				-- Set IsProc parameter
			    CALL public."p_IsProc"(TRUE);
				--
				BEGIN
				-- Update data from cl_Roles table
				UPDATE public.%I SET "IsActive" = NOW() WHERE "ID" = _id;
				--
				EXCEPTION WHEN OTHERS THEN
					-- Reset IsProc parameter
				    CALL public."p_IsProc"(FALSE);
					
					-- Reset CurrentThread parameter
					CALL public."p_CurrentThread"(FALSE);
					RETURN;
				END;
				-- Reset IsProc parameter
			    CALL public."p_IsProc"(FALSE);
				
				-- Reset CurrentThread parameter
				CALL public."p_CurrentThread"(FALSE);
			END;
			$$;
		', _procname, _tablename);
		_procname := 'Delete_Role_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
			    BEFORE DELETE
			    ON public.%I
			    FOR EACH ROW
			    EXECUTE FUNCTION public."t_DeleteTrigger"();
		', _procname, _tablename);
		_procname := 'Insert_Role_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
			    BEFORE INSERT 
			    ON public.%I
			    FOR EACH ROW
			    EXECUTE FUNCTION public."t_InsertTrigger"();
		', _procname, _tablename);
		_procname := 'Update_Role_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
			    BEFORE UPDATE 
			    ON public.%I
			    FOR EACH ROW
			    EXECUTE FUNCTION public."t_UpdateTrigger"();
		', _procname, _tablename);
		_procname := 'Log_Role_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
			    AFTER INSERT OR UPDATE OR DELETE 
			    ON public.%I
			    FOR EACH ROW
			    EXECUTE FUNCTION public."t_LogAudit"();
		', _procname, _tablename);
		_procname := 'Release_Role_' || _name;
		EXECUTE FORMAT('
			CREATE OR REPLACE TRIGGER %I
				AFTER INSERT OR UPDATE OR DELETE
				ON public.%I
				FOR EACH ROW
				EXECUTE FUNCTION public."t_ReleaseThread"();
		', _procname, _tablename);
	--
	EXCEPTION WHEN OTHERS THEN
		-- Reset IsProc parameter
	    CALL public."p_IsProc"(FALSE);
		
		-- Reset CurrentThread parameter
		CALL public."p_CurrentThread"(FALSE);
		RETURN;
	END;
	-- Reset IsProc parameter
    CALL public."p_IsProc"(FALSE);
	
	-- Reset CurrentThread parameter
	CALL public."p_CurrentThread"(FALSE);
END;
$BODY$;

-- Trigger: Delete_Role

CREATE OR REPLACE TRIGGER "Delete_Role"
    BEFORE DELETE
    ON public."cl_Roles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Role

CREATE OR REPLACE TRIGGER "Insert_Role"
    BEFORE INSERT
    ON public."cl_Roles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Update_Role

CREATE OR REPLACE TRIGGER "Update_Role"
    BEFORE UPDATE 
    ON public."cl_Roles"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_UpdateTrigger"();

-- Table: public.cl_RoleRelations

CREATE TABLE IF NOT EXISTS public."cl_RoleRelations"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "CredentialID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Credentials" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "RoleID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Roles" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "IsActive" timestamp without time zone,
    "Description" text COLLATE pg_catalog."default",
    CONSTRAINT "UQ_RoleRelation" UNIQUE ("CredentialID", "RoleID")
)

TABLESPACE pg_default;

-- PROCEDURE: public.p_InsertRoleRelation(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertRoleRelation"(
	IN _credentialid character varying(50),
	IN _roleid character varying(50),
	IN _description text DEFAULT NULL::text)
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_RoleRelations'; _id character varying(50) := '%s';
BEGIN
	-- Format sql
	_sql := FORMAT('INSERT INTO public.%I VALUES (%s, %L, %L, NULL, %L);', _tablename, _id, _credentialid, _roleid, _description);
	-- Execute sql
	CALL public."p_Query"(_sql, _tablename, 'ROR');
END;
$BODY$;

-- PROCEDURE: public.p_DeleteRoleRelation(character varying)

CREATE OR REPLACE PROCEDURE public."p_DeleteRoleRelation"(
	IN _id character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _sql text; _tablename character varying(50) := 'cl_RoleRelations';
BEGIN
	-- Format sql
	_sql := FORMAT('DELETE FROM public.%I WHERE "ID" = %L', _id);
END;
$BODY$;

-- Trigger: Update_RoleRelation

CREATE OR REPLACE TRIGGER "Update_RoleRelation"
	BEFORE UPDATE
	ON public."cl_RoleRelations"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_RoleRelation

CREATE OR REPLACE TRIGGER "Insert_RoleRelation"
    BEFORE INSERT
    ON public."cl_RoleRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Trigger: Remove_RoleRelation

CREATE OR REPLACE TRIGGER "Remove_RoleRelation"
    BEFORE DELETE
    ON public."cl_RoleRelations"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_RemoveTrigger"();

-- Table: public.cl_Trackings

CREATE TABLE IF NOT EXISTS public."cl_Trackings"
(
    "ID" character varying(50) COLLATE pg_catalog."default" PRIMARY KEY,
    "CredentialID" character varying(50) COLLATE pg_catalog."default" NOT NULL REFERENCES public."cl_Credentials" ("ID") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION,
    "Action" character varying(50) COLLATE pg_catalog."default" NOT NULL,
	"IP" character varying(50) COLLATE pg_catalog."default" NOT NULL,
    "ActionDate" timestamp without time zone NOT NULL
)

TABLESPACE pg_default;

-- FUNCTION: public.t_CreditLog()

CREATE OR REPLACE FUNCTION public."t_CreditLog"()
	RETURNS trigger
	LANGUAGE 'plpgsql'
	COST 100
	VOLATILE NOT LEAKPROOF
AS $BODY$
BEGIN
	IF NEW."Action" = 'LOGIN_ATTEMPT' THEN CALL public."p_InsertTracking"(NEW."ID", NEW."LoginStatus", NEW."IP"); END IF;
	RETURN NEW;
END;
$BODY$;

-- PROCEDURE: public.p_InsertTracking(character varying, character varying, text)

CREATE OR REPLACE PROCEDURE public."p_InsertTracking"(
	IN _credentialid character varying(50),
	IN _action character varying(50),
	IN _IP character varying(50))
LANGUAGE 'plpgsql'
AS $BODY$
DECLARE _id character varying(50); _threaded boolean; _processed boolean;
BEGIN
	-- Set CurrentThread parameter
	SELECT public."f_CurrentThread"() INTO _threaded;
	IF _threaded = FALSE THEN CALL public."p_CurrentThread"(TRUE); END IF;
	
	-- Set IsProc parameter
	SELECT public."f_IsProc"() INTO _processed;
    IF _processed = FALSE THEN CALL public."p_IsProc"(TRUE); END IF;
	
	-- Insert into the trackings table
	BEGIN
		-- Create ID
		_id := public."f_CreateID"('TRK', 'cl_Trackings');
		-- Insert data to cl_Trakings table
		INSERT INTO public."cl_Trackings" VALUES (_id, _credentialid, _action, _IP, NOW());
	--
	EXCEPTION WHEN OTHERS THEN
		-- Reset IsProc parameter
	    CALL public."p_IsProc"(FALSE);
		
		-- Reset CurrentThread parameter
		CALL public."p_CurrentThread"(FALSE);
		RETURN;
	END;
	-- Reset IsProc parameter
    IF _processed = FALSE THEN CALL public."p_IsProc"(FALSE); END IF;
	
	-- Reset CurrentThread parameter
	IF _threaded = FALSE THEN CALL public."p_CurrentThread"(FALSE); END IF;
END;
$BODY$;

-- Trigger: Delete_Tracking

CREATE OR REPLACE TRIGGER "Delete_Tracking"
    BEFORE DELETE OR UPDATE
    ON public."cl_Trackings"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_DeleteTrigger"();

-- Trigger: Insert_Tracking

CREATE OR REPLACE TRIGGER "Insert_Tracking"
    BEFORE INSERT 
    ON public."cl_Trackings"
    FOR EACH ROW
    EXECUTE FUNCTION public."t_InsertTrigger"();

-- Initial data

DO
$BODY$
DECLARE _profileid character varying(50); _langid character varying(50); _id character varying(50); _pwd character varying(50);
BEGIN

-- App registry

CALL public."p_InsertApp"('Profiling');

-- Insert Profiles

SELECT "ID" INTO _id FROM public."cl_Countries" WHERE "ISO3" = 'CMR';
SELECT "ID" INTO _profileid FROM public."cl_Cities" WHERE "Name" = 'DLA';
CALL public."p_InsertProfile"('Jéoline', LOCALTIMESTAMP, _id, _profileid);
CALL public."p_InsertProfile"('Unknown', LOCALTIMESTAMP, _id, _profileid);
CALL public."p_InsertProfile"('Administrator', LOCALTIMESTAMP, _id, _profileid);
CALL public."p_InsertProfile"('GWET', LOCALTIMESTAMP, _id, _profileid, 'Bell Béa');
CALL public."p_InsertProfile"('NDEDI PENDA', LOCALTIMESTAMP, _id, _profileid, 'Gaëlla');
CALL public."p_InsertProfile"('CARLE', LOCALTIMESTAMP, _id, _profileid, 'Julia');
CALL public."p_InsertProfile"('NJOLLE NGANGUE', LOCALTIMESTAMP, _id, _profileid, 'Nancy');
--
SELECT "ID" INTO _id FROM public."cl_Profiles" WHERE "LastName" = 'Unknown';
CALL public."p_DeleteProfile"(_id);

-- Insert titles

CALL public."p_InsertTitle"('Non applicable');
CALL public."p_InsertTitle"('Doctor');
CALL public."p_InsertTitle"('Professor');

-- Insert TitleRelations

SELECT "ID" INTO _id FROM public."cl_Titles" WHERE "Code" = 1;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
CALL public."p_InsertTitleRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Administrator';
CALL public."p_InsertTitleRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'CARLE';
CALL public."p_InsertTitleRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NJOLLE NGANGUE';
CALL public."p_InsertTitleRelation"(_id, _profileid);
--
SELECT "ID" INTO _id FROM public."cl_Titles" WHERE "Code" = 2;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'GWET';
CALL public."p_InsertTitleRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NDEDI PENDA';
CALL public."p_InsertTitleRelation"(_id, _profileid);

-- Insert Statuses

CALL public."p_InsertStatus"('Non applicable');
CALL public."p_InsertStatus"('Single');
CALL public."p_InsertStatus"('Married');
CALL public."p_InsertStatus"('Divorced');
CALL public."p_InsertStatus"('Widow');

-- Insert StatusRelations

SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Code" = 1;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
CALL public."p_InsertStatusRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Administrator';
CALL public."p_InsertStatusRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'GWET';
CALL public."p_InsertStatusRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NDEDI PENDA';
CALL public."p_InsertStatusRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'CARLE';
CALL public."p_InsertStatusRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NJOLLE NGANGUE';
CALL public."p_InsertStatusRelation"(_id, _profileid);

-- Insert Genders

CALL public."p_InsertGender"('Non applicable');
CALL public."p_InsertGender"('Male');
CALL public."p_InsertGender"('Female');

-- Insert GenderRelations

SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 1;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
CALL public."p_InsertGenderRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Administrator';
CALL public."p_InsertGenderRelation"(_id, _profileid);
--
SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 2;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'GWET';
CALL public."p_InsertGenderRelation"(_id, _profileid);
--
SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Code" = 3;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NDEDI PENDA';
CALL public."p_InsertGenderRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'CARLE';
CALL public."p_InsertGenderRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NJOLLE NGANGUE';
CALL public."p_InsertGenderRelation"(_id, _profileid);

-- Insert Civilities

CALL public."p_InsertCivility"('Non applicable');
CALL public."p_InsertCivility"('Mister');
CALL public."p_InsertCivility"('Madam');
CALL public."p_InsertCivility"('Miss');

-- Insert CivilityRelations

SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 1;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
CALL public."p_InsertCivilityRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Administrator';
CALL public."p_InsertCivilityRelation"(_id, _profileid);
--
SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 2;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'GWET';
CALL public."p_InsertCivilityRelation"(_id, _profileid);
--
SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Code" = 4;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NDEDI PENDA';
CALL public."p_InsertCivilityRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'CARLE';
CALL public."p_InsertCivilityRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NJOLLE NGANGUE';
CALL public."p_InsertCivilityRelation"(_id, _profileid);

-- Insert Occupations

CALL public."p_InsertOccupation"('Non applicable');
CALL public."p_InsertOccupation"('Gynecologist');
CALL public."p_InsertOccupation"('Obstetrician');
CALL public."p_InsertOccupation"('Nurse');
CALL public."p_InsertOccupation"('Lab technician');
CALL public."p_InsertOccupation"('Anesthetist');

-- Insert OccupationRelations

SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Code" = 1;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
CALL public."p_InsertOccupationRelation"(_id, _profileid);
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Administrator';
CALL public."p_InsertOccupationRelation"(_id, _profileid);
--
SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Code" = 2;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'GWET';
CALL public."p_InsertOccupationRelation"(_id, _profileid);
--
SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Code" = 3;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'GWET';
CALL public."p_InsertOccupationRelation"(_id, _profileid);
--
SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Code" = 6;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NDEDI PENDA';
CALL public."p_InsertOccupationRelation"(_id, _profileid);
--
SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Code" = 4;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'CARLE';
CALL public."p_InsertOccupationRelation"(_id, _profileid);
--
SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Code" = 5;
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NJOLLE NGANGUE';
CALL public."p_InsertOccupationRelation"(_id, _profileid);

-- Insert ContactTypes

CALL public."p_InsertContactType"('Phone');
CALL public."p_InsertContactType"('Email');
CALL public."p_InsertContactType"('Address');
CALL public."p_InsertContactType"('WhatsApp');
CALL public."p_InsertContactType"('Telegram');
CALL public."p_InsertContactType"('YouTube');
CALL public."p_InsertContactType"('Instagram');
CALL public."p_InsertContactType"('Twitter');
CALL public."p_InsertContactType"('LinkedIn');

-- Insert Contacts

SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Email';
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
CALL public."p_InsertContact"(_id, _profileid, 'Jéoline email');
SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Phone';
CALL public."p_InsertContact"(_id, _profileid, 'Jéoline phone');
SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Address';
CALL public."p_InsertContact"(_id, _profileid, 'Jéoline location');
--
SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Email';
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'GWET';
CALL public."p_InsertContact"(_id, _profileid, 'Bell Béa GWET email');
--
SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Email';
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NDEDI PENDA';
CALL public."p_InsertContact"(_id, _profileid, 'Gaëlla NDEDI PENDA email');
--
SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Email';
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'CARLE';
CALL public."p_InsertContact"(_id, _profileid, 'Julia CARLE email');
--
SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Email';
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NJOLLE NGANGUE';
CALL public."p_InsertContact"(_id, _profileid, 'Nancy NJOLLE NGANGUE email');

-- Insert ContactRelations

SELECT "ID" INTO _langid FROM public."cl_Languages" WHERE "Label" = 'US';
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline email';
CALL public."p_InsertContactRelation"(_langid, _id, 'infos@jeolinecorporates.com', 'Email_round_black');
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline phone';
CALL public."p_InsertContactRelation"(_langid, _id, '+237675507158', 'Phone_round_black');
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline location';
CALL public."p_InsertContactRelation"(_langid, _id, '264 de la Motte-Picquet Street, Bonanjo', 'Location_round_black');
--
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Bell Béa GWET email';
CALL public."p_InsertContactRelation"(_langid, _id, 'bellbeagwet@cliniqueodyssee.com', 'Email_round_black');
--
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Gaëlla NDEDI PENDA email';
CALL public."p_InsertContactRelation"(_langid, _id, 'ndedipendagaella@gmail.com', 'Email_round_black');
--
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Julia CARLE email';
CALL public."p_InsertContactRelation"(_langid, _id, 'carlejulia@gmail.com', 'Email_round_black');
--
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Nancy NJOLLE NGANGUE email';
CALL public."p_InsertContactRelation"(_langid, _id, 'nancynjolle.odyssee@gmail.com', 'Email_round_black');

--
SELECT "ID" INTO _langid FROM public."cl_Languages" WHERE "Label" = 'GB';
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline email';
CALL public."p_InsertContactRelation"(_langid, _id, 'infos@jeolinecorporates.com', 'Email_round_black');
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline phone';
CALL public."p_InsertContactRelation"(_langid, _id, '+237675507158', 'Phone_round_black');
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline location';
CALL public."p_InsertContactRelation"(_langid, _id, '264 de la Motte-Picquet Street, Bonanjo', 'Location_round_black');
--
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Bell Béa GWET email';
CALL public."p_InsertContactRelation"(_langid, _id, 'bellbeagwet@cliniqueodyssee.com', 'Email_round_black');
--
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Gaëlla NDEDI PENDA email';
CALL public."p_InsertContactRelation"(_langid, _id, 'ndedipendagaella@gmail.com', 'Email_round_black');
--
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Julia CARLE email';
CALL public."p_InsertContactRelation"(_langid, _id, 'carlejulia@gmail.com', 'Email_round_black');
--
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Nancy NJOLLE NGANGUE email';
CALL public."p_InsertContactRelation"(_langid, _id, 'nancynjolle.odyssee@gmail.com', 'Email_round_black');

--
SELECT "ID" INTO _langid FROM public."cl_Languages" WHERE "Label" = 'FR';
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline email';
CALL public."p_InsertContactRelation"(_langid, _id, 'infos@jeolinecorporates.com', 'Email_round_black');
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline phone';
CALL public."p_InsertContactRelation"(_langid, _id, '+237675507158', 'Phone_round_black');
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Jéoline location';
CALL public."p_InsertContactRelation"(_langid, _id, '264 rue de la Motte-Picquet, Bonanjo', 'Location_round_black');
--
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Bell Béa GWET email';
CALL public."p_InsertContactRelation"(_langid, _id, 'bellbeagwet@cliniqueodyssee.com', 'Email_round_black');
--
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Gaëlla NDEDI PENDA email';
CALL public."p_InsertContactRelation"(_langid, _id, 'ndedipendagaella@gmail.com', 'Email_round_black');
--
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Julia CARLE email';
CALL public."p_InsertContactRelation"(_langid, _id, 'carlejulia.odysse@gmail.com', 'Email_round_black');
--
SELECT "ID" INTO _id FROM public."cl_Contacts" WHERE "Name" = 'Nancy NJOLLE NGANGUE email';
CALL public."p_InsertContactRelation"(_langid, _id, 'nancynjolle.odyssee@gmail.com', 'Email_round_black');

-- Insert Credentials

SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Jéoline';
CALL public."p_InsertCredential"(_pwd, _profileid, 'infos@jeolinecorporates.com');
SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "ProfileID" = _profileid;
CALL public."p_UpdatePassword"(_id, _pwd, 'pat1380/*56');
--
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Unknown';
CALL public."p_InsertCredential"(_pwd, _profileid, 'unkown@unkown.com');
SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "ProfileID" = _profileid;
CALL public."p_DeleteCredential"(_id);
--
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'Administrator';
CALL public."p_InsertCredential"(_pwd, _profileid, 'admin@cashledger.com');
SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "ProfileID" = _profileid;
CALL public."p_UpdatePassword"(_id, _pwd, 'admin1234');
--
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'GWET';
CALL public."p_InsertCredential"(_pwd, _profileid, 'bellbeagwet@cliniqueodyssee.com');
SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "ProfileID" = _profileid;
CALL public."p_UpdatePassword"(_id, _pwd, 'U7JvtsKTcB');
--
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NDEDI PENDA';
CALL public."p_InsertCredential"(_pwd, _profileid, 'ndedipendagaella@gmail.com');
SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "ProfileID" = _profileid;
CALL public."p_UpdatePassword"(_id, _pwd, 'BnpHd8ZR5z');
--
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'CARLE';
CALL public."p_InsertCredential"(_pwd, _profileid, 'carlejulia.odysse@gmail.com');
SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "ProfileID" = _profileid;
CALL public."p_UpdatePassword"(_id, _pwd, 'pVAf75D4yq');
--
SELECT "ID" INTO _profileid FROM public."cl_Profiles" WHERE "LastName" = 'NJOLLE NGANGUE';
CALL public."p_InsertCredential"(_pwd, _profileid, 'nancynjolle.odyssee@gmail.com');
SELECT "ID" INTO _id FROM public."cl_Credentials" WHERE "ProfileID" = _profileid;
CALL public."p_UpdatePassword"(_id, _pwd, 'M3Q82Prj5v');

-- Insert Permissions

CALL public."p_InsertPermission"('All', 'A', 'Full resource permission');
CALL public."p_InsertPermission"('None', 'N', 'No resource permission');
CALL public."p_InsertPermission"('Create', 'C', 'Create data permission');
CALL public."p_InsertPermission"('Read', 'R', 'Read data permission');
CALL public."p_InsertPermission"('Update', 'U', 'Update data permission');
CALL public."p_InsertPermission"('Delete', 'D', 'Delete data permission');
CALL public."p_InsertPermission"('Execute', 'X', 'Execute app permission');

-- Insert Roles

CALL public."p_InsertRole"('Administrator');

-- Insert RoleRelations

SELECT "ID" INTO _profileid FROM public."cl_Credentials" WHERE "UserName" = 'infos@jeolinecorporates.com';
SELECT "ID" INTO _id FROM public."cl_Roles" WHERE "Name" = 'Administrator';
CALL public."p_InsertRoleRelation"(_profileid, _id);
SELECT "ID" INTO _profileid FROM public."cl_Credentials" WHERE "UserName" = 'admin@cashledger.com';
CALL public."p_InsertRoleRelation"(_profileid, _id);
SELECT "ID" INTO _profileid FROM public."cl_Credentials" WHERE "UserName" = 'bellbeagwet@cliniqueodyssee.com';
CALL public."p_InsertRoleRelation"(_profileid, _id);
SELECT "ID" INTO _profileid FROM public."cl_Credentials" WHERE "UserName" = 'ndedipendagaella@gmail.com';
CALL public."p_InsertRoleRelation"(_profileid, _id);
SELECT "ID" INTO _profileid FROM public."cl_Credentials" WHERE "UserName" = 'carlejulia.odysse@gmail.com';
CALL public."p_InsertRoleRelation"(_profileid, _id);
SELECT "ID" INTO _profileid FROM public."cl_Credentials" WHERE "UserName" = 'nancynjolle.odyssee@gmail.com';
CALL public."p_InsertRoleRelation"(_profileid, _id);

-- Insert Permissions tokens

SELECT "ID" INTO _id FROM public."cl_Roles" WHERE "Name" = 'Administrator';
CALL public."p_InsertRole_Administrator"(_id, 'All', 'All', 'A', 'All');

END $BODY$;

-- Insert LanguageRelations

DO
$BODY$
DECLARE _id character varying(50); _appid character varying(50); _us character varying(50); _gb character varying(50); _fr character varying(50); _es character varying(50); _ar character varying(50);

BEGIN
	SELECT "ID" INTO _us FROM public."cl_Languages" WHERE "Label" = 'US';
	SELECT "ID" INTO _gb FROM public."cl_Languages" WHERE "Label" = 'GB';
	SELECT "ID" INTO _fr FROM public."cl_Languages" WHERE "Label" = 'FR';
	SELECT "ID" INTO _es FROM public."cl_Languages" WHERE "Label" = 'ES';
	SELECT "ID" INTO _ar FROM public."cl_Languages" WHERE "Label" = 'AR';

	-- App

	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Profiling';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Profiling');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Profiling');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Gestion des utilisateurs');
	
	-- App category
	
	IF NOT EXISTS(SELECT 1 FROM public."cl_AppCategories" WHERE "Name" = 'User management') THEN
		CALL public."p_InsertAppCategory"('User management');
		SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'User management';
		CALL public."p_InsertLanguageRelation"(_us, _appid, 'User management');
		CALL public."p_InsertLanguageRelation"(_gb, _appid, 'User management');
		CALL public."p_InsertLanguageRelation"(_fr, _appid, 'Gestion des utilisateurs');
	END IF;
	--
	SELECT "ID" INTO _id FROM public."cl_Apps" WHERE "Name" = 'Profiling';
	SELECT "ID" INTO _appid from public."cl_AppCategories" WHERE "Name" = 'User management';
	CALL public."p_InsertAppRelation"(_id, _appid);
	
	-- Titles
	
	SELECT "ID" INTO _id FROM public."cl_Titles" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	--
	SELECT "ID" INTO _id FROM public."cl_Titles" WHERE "Name" = 'Doctor';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Docteur');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Doctor');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Doctor');
	--
	SELECT "ID" INTO _id FROM public."cl_Titles" WHERE "Name" = 'Professor';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Professeur');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Professor');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Professor');
	
	-- Statuses
	
	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Name" = 'Single';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Single');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Single');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Célibataire');
	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Name" = 'Married';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Married');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Married');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Marié(e)');
	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Name" = 'Divorced';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Divorced');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Divorced');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Divorcé(e)');
	SELECT "ID" INTO _id FROM public."cl_Statuses" WHERE "Name" = 'Widow';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Widow');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Widow');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Veuf(ve)');
	
	-- Genders
	
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Name" = 'Male';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Male');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Male');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Homme');
	SELECT "ID" INTO _id FROM public."cl_Genders" WHERE "Name" = 'Female';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Female');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Female');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Femme');
	
	-- Civilities
	
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Name" = 'Mister';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Mister');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Mister');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Monsieur');
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Name" = 'Madam';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Madam');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Madam');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Madame');
	SELECT "ID" INTO _id FROM public."cl_Civilities" WHERE "Name" = 'Miss';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Miss');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Miss');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Mademoiselle');
	
	-- Occupations
	
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Name" = 'Non applicable';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	--
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Name" = 'Gynecologist';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Non applicable');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Non applicable');
	--
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Name" = 'Obstetrician';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Obstetrician');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Obstetrician');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Obstétricien');
	--
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Name" = 'Nurse';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Nurse');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Nurse');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Infirmier');
	--
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Name" = 'Lab technician';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Lab technician');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Lab technician');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Laborantin');
	--
	SELECT "ID" INTO _id FROM public."cl_Occupations" WHERE "Name" = 'Anesthetist';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Anesthetist');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Anesthetist');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'anésthésiste');
	
	-- ContactTypes
	
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Phone';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Phone');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Phone');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Téléphone');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Email';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Email');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Email');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Email');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Address';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Location');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Location');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Localisation');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'WhatsApp';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'WhatsApp');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'WhatsApp');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'WhatsApp');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Telegram';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Telegram');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Telegram');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Telegram');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'YouTube';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'YouTube');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'YouTube');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'YouTube');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Instagram';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Instagram');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Instagram');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Instagram');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'Twitter';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Twitter');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Twitter');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Twitter');
	SELECT "ID" INTO _id FROM public."cl_ContactTypes" WHERE "Name" = 'LinkedIn';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'LinkedIn');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'LinkedIn');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'LinkedIn');
	
	-- Roles
	
	SELECT "ID" INTO _id FROM public."cl_Roles" WHERE "Name" = 'Administrator';
	CALL public."p_InsertLanguageRelation"(_us, _id, 'Administrator');
	CALL public."p_InsertLanguageRelation"(_gb, _id, 'Administrator');
	CALL public."p_InsertLanguageRelation"(_fr, _id, 'Administrateur');
	
END $BODY$;

-- Trigger: Credential_Log

CREATE OR REPLACE TRIGGER "Credential_Log"
	AFTER UPDATE
	ON public."cl_Credentials"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_CreditLog"();

-- Trigger: Log_Credential

CREATE OR REPLACE TRIGGER "Log_Credential"
	AFTER INSERT OR UPDATE OR DELETE
	ON public."cl_Credentials"
	FOR EACH ROW
	EXECUTE FUNCTION public."t_LogCredential"();

-- Trigger: Log_Audit
DO
$BODY$
DECLARE _tablename text; _triggername text;
	_tablenames text[] := ARRAY['cl_AppCategories', 'cl_AppRelations', 'cl_Apps', 'cl_Cities', 'cl_Civilities', 'cl_CivilityRelations', 'cl_ContactRelations', 'cl_Contacts', 'cl_ContactTypes', 'cl_Continents',
	'cl_Countries', 'cl_Genders', 'cl_GenderRelations', 'cl_Languages', 'cl_LanguageRelations', 'cl_OccupationRelations', 'cl_Occupations', 'cl_Parameters', 'cl_ParameterRelations', 'cl_Permissions',
	'cl_Profiles', 'cl_RoleRelations', 'cl_Roles', 'cl_Statuses', 'cl_StatusRelations', 'cl_TitleRelations', 'cl_Titles'];
BEGIN
	FOR _tablename IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
	LOOP
		IF _tablename != ALL(_tablenames) THEN CONTINUE; END IF;
		_triggername :=
			CASE
				WHEN _tablename ~ 'ies$' THEN REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 3), 'ies$', 'y')
				WHEN _tablename = 'cl_Statuses' THEN 'Status'
				ELSE REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 1), 's$', '')
			END;
		_triggername := 'Log_' || _triggername;
		IF _triggername NOT IN (SELECT tgname FROM pg_trigger WHERE tgname ~ 'Log_') THEN
			EXECUTE FORMAT('
				CREATE OR REPLACE TRIGGER %I
					AFTER INSERT OR UPDATE OR DELETE
					ON public.%I
					FOR EACH ROW
					EXECUTE FUNCTION public."t_LogAudit"();
			', _triggername, _tablename);
		END IF;
		--
		_triggername :=
			CASE
				WHEN _tablename ~ 'ies$' THEN REGEXP_REPLACE(SUBSTRING(_tablename FROM 4 FOR LENGTH(_tablename) - 3), 'ies$', 'y')
				WHEN _tablename = 'cl_Statuses' THEN 'Status'
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