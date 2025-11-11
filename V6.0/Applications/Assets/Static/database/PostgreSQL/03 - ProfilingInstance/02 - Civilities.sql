/* Profiling app */

/* Titles, TitleRelations, Statuses, StatusRelations, Genders, GenderRelations, Civilities, CivilityRelations, Occupations, OccupationRelations */

-- Table: public.cl_Titles

DROP TABLE IF EXISTS public."cl_Titles";
CREATE TABLE public."cl_Titles"
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

DROP TABLE IF EXISTS public."cl_TitleRelations";
CREATE TABLE public."cl_TitleRelations"
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

DROP TABLE IF EXISTS public."cl_Statuses";
CREATE TABLE public."cl_Statuses"
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

DROP TABLE IF EXISTS public."cl_StatusRelations";
CREATE TABLE public."cl_StatusRelations"
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

DROP TABLE IF EXISTS public."cl_Genders";
CREATE TABLE public."cl_Genders"
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

DROP TABLE IF EXISTS public."GenderRelations";
CREATE TABLE public."cl_GenderRelations"
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

DROP TABLE IF EXISTS public."cl_Civilities";
CREATE TABLE public."cl_Civilities"
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

DROP TABLE IF EXISTS public."cl_CivilityRelations";
CREATE TABLE public."cl_CivilityRelations"
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

DROP TABLE IF EXISTS public."cl_Occupations"
CREATE TABLE public."cl_Occupations"
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

DROP TABLE IF EXISTS public."cl_OccupationRelations";
CREATE TABLE public."cl_OccupationRelations"
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